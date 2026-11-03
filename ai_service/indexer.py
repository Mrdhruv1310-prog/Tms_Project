import os
import pymysql
import chromadb
from dotenv import load_dotenv
from google import genai

load_dotenv()

ai_client = genai.Client(api_key=os.getenv("GEMINI_API_KEY"))
chroma = chromadb.PersistentClient(path="./chroma_db")
collection = chroma.get_or_create_collection(name="tms_tasks")

def get_db():
    return pymysql.connect(
        host=os.getenv("DB_HOST", "127.0.0.1"),
        user=os.getenv("DB_USER", "root"),
        password=os.getenv("DB_PASSWORD", ""),
        database=os.getenv("DB_NAME", "n_tms"),
        cursorclass=pymysql.cursors.DictCursor
    )

def sync_tasks_to_vector_db():
    conn = get_db()
    try:
        with conn.cursor() as cursor:
            cursor.execute("SELECT id, title, description, user_id FROM tasks;")
            tasks = cursor.fetchall()

        for task in tasks:
            text_to_embed = f"Title: {task['title']}\nDescription: {task.get('description', '')}"
            
            # Generate embedding via Gemini
            emb = ai_client.models.embed_content(
                model="text-embedding-004",
                contents=text_to_embed
            )
            
            collection.upsert(
                ids=[str(task["id"])],
                embeddings=[emb.embedding.values],
                documents=[text_to_embed],
                metadatas=[{"task_id": task["id"], "user_id": task["user_id"]}]
            )
        print(f"Successfully indexed {len(tasks)} tasks into ChromaDB.")
    finally:
        conn.close()

if __name__ == "__main__":
    sync_tasks_to_vector_db()