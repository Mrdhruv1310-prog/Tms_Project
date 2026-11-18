import os
import pymysql
from dotenv import load_dotenv
from fastapi import FastAPI, HTTPException
from fastapi.middleware.cors import CORSMiddleware
from google import genai
from google.genai import types
from pydantic import BaseModel

load_dotenv()

app = FastAPI(title="Elektro TMS AI Assistant")

app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],
    allow_credentials=True,
    allow_methods=["*"],
    allow_headers=["*"],
)

# Initialize Gemini Client
api_key = os.getenv("GEMINI_API_KEY")
ai_client = genai.Client(api_key=api_key)

# Model configuration
AI_MODEL = os.getenv("GEMINI_MODEL", "gemini-3.5-flash-lite")

def get_db():
    return pymysql.connect(
        host=os.getenv("DB_HOST", "127.0.0.1"),
        port=int(os.getenv("DB_PORT", 3306)),
        user=os.getenv("DB_USER", "root"),
        password=os.getenv("DB_PASSWORD", ""),
        database=os.getenv("DB_NAME", "tms_db"),
        cursorclass=pymysql.cursors.DictCursor
    )

class ChatRequest(BaseModel):
    user_id: int
    role: str = "employee"
    message: str

def fetch_live_task_context(user_id: int, role: str) -> str:
    """Retrieves live tasks from MySQL filtered by user access role."""
    conn = get_db()
    try:
        with conn.cursor() as cursor:
            # Respect soft deletes if column exists
            cursor.execute("SHOW COLUMNS FROM tasks LIKE 'deleted_at';")
            has_deleted_at = cursor.fetchone() is not None
            soft_delete_clause = "WHERE t.deleted_at IS NULL" if has_deleted_at else "WHERE 1=1"

            # Check if due_date column exists
            cursor.execute("SHOW COLUMNS FROM tasks LIKE 'due_date';")
            has_due_date = cursor.fetchone() is not None
            due_col = "t.due_date" if has_due_date else "NULL as due_date"

            if role.lower() in ["admin", "superadmin", "super-admin", "manager"]:
                sql = f"""
                    SELECT 
                        t.id, 
                        t.title, 
                        t.status, 
                        t.priority, 
                        {due_col}, 
                        t.created_at,
                        COALESCE(
                            GROUP_CONCAT(DISTINCT CONCAT(u.first_name, ' ', COALESCE(u.last_name, '')) SEPARATOR ', '),
                            'Unassigned'
                        ) AS assigned_employees
                    FROM tasks t
                    LEFT JOIN task_assignments ta ON t.id = ta.task_id
                    LEFT JOIN users u ON ta.user_id = u.id
                    {soft_delete_clause}
                    GROUP BY t.id
                    ORDER BY t.created_at DESC
                    LIMIT 35;
                """
                cursor.execute(sql)
            else:
                sql = f"""
                    SELECT 
                        t.id, 
                        t.title, 
                        t.status, 
                        t.priority, 
                        {due_col}, 
                        t.created_at,
                        COALESCE(
                            GROUP_CONCAT(DISTINCT CONCAT(u.first_name, ' ', COALESCE(u.last_name, '')) SEPARATOR ', '),
                            'Unassigned'
                        ) AS assigned_employees
                    FROM tasks t
                    LEFT JOIN task_assignments ta ON t.id = ta.task_id
                    LEFT JOIN users u ON ta.user_id = u.id
                    {soft_delete_clause} AND (ta.user_id = %s OR t.user_id = %s)
                    GROUP BY t.id
                    ORDER BY t.created_at DESC
                    LIMIT 25;
                """
                cursor.execute(sql, (user_id, user_id))

            rows = cursor.fetchall()
            if not rows:
                return "DATABASE_STATUS: No task records found in the database."

            formatted = []
            for r in rows:
                due = r.get("due_date") or "No deadline"
                status = r.get("status") or "pending"
                prio = r.get("priority") or "normal"
                assignees = r.get("assigned_employees") or "Unassigned"

                formatted.append(
                    f"Task #{r['id']} | Title: \"{r['title']}\" | Assigned To: {assignees} | Status: {status} | Priority: {prio} | Due: {due}"
                )
            return "\n".join(formatted)

    except Exception as err:
        print(f"Database query error: {err}")
        return "DATABASE_STATUS: Database connection is temporarily unavailable."
    finally:
        conn.close()

@app.get("/")
def health():
    return {"status": "ok", "service": "Elektro", "model": AI_MODEL}

@app.post("/api/chat")
async def chat_handler(req: ChatRequest):
    try:
        tasks_context = fetch_live_task_context(req.user_id, req.role)

        system_instruction = f"""
You are Elektro, an intelligent, respectful, and helpful AI assistant embedded in the Task Management System (TMS).
Current Session: User ID {req.user_id} (Role: {req.role.upper()})

LIVE TASK RECORDS (FROM DATABASE):
{tasks_context}

BEHAVIOR AND INTERACTION GUIDELINES:
1. Tone & Persona:
   - Be polite, courteous, respectful, and conversational.
   - For greetings (e.g., "hi", "hello", "good morning"), welcome the user warmly and offer to help them with their tasks, deadlines, or priorities.
   - For general inquiries about your capabilities, explain clearly how you can help them view and filter tasks.

2. Task & Database Queries:
   - When asked about tasks, deadlines, assignees, or priorities, answer using ONLY the live task records provided above.
   - Never invent or assume tasks, assignees, IDs, or dates that are not in the context.
   - If a specific employee or task is not found in the records, explain politely: "I couldn't find any tasks matching that in the system."

3. Formatting & Clarity:
   - Keep answers clean, organized, and easy to read.
   - Use bold text for key fields and structured bullet points for task lists:
     • **Task #[ID]: [Title]** — Assigned to: [Assignee] | Status: `[Status]` | Priority: `[Priority]` | Due: `[Due]`
   - Avoid long walls of text; keep summaries focused and readable.
"""

        response = ai_client.models.generate_content(
            model=AI_MODEL,
            contents=req.message,
            config=types.GenerateContentConfig(
                system_instruction=system_instruction,
                temperature=0.3,          # Balanced: natural conversation without hallucinating facts
                max_output_tokens=600,    # Plenty of space for multi-task lists and clear formatting
            )
        )

        return {"reply": response.text}

    except Exception as e:
        print(f"Chat generation error: {e}")
        raise HTTPException(status_code=500, detail=str(e))