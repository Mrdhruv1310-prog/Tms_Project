<?php

namespace App\Livewire;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\On;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use App\Mail\RegisterUserMail;
use Shuchkin\SimpleXLSX;

#[Layout('components.layouts.app')]
#[Title('Employee List')]
class Users extends Component
{
    use WithFileUploads;

    /** @var \Illuminate\Database\Eloquent\Collection<int, User>|array */
    public $users;

    // File Upload Property
    public $excelFile = null;

    // Selected Target User ID
    public ?int $selectedUserId = null;

    // Modal control
    public bool $userDetailsModalOpen = false;
    public ?User $selectedUser = null;

    public function mount(): void
    {
        $this->authorizeAccess();
        $this->loadUsers();
    }

    #[On('usercreated')]
    #[On('userupdated')]
    #[On('userdeleted')]
    public function handleUserRefresh(): void
    {
        $this->loadUsers();
    }

    private function authorizeAccess(): void
    {
        /** @var User|null $currentUser */
        $currentUser = Auth::user();

        $allowedRoles = ['super-admin', 'admin', 'hr', 'manager', 'employee'];

        if (!Auth::check() || !$currentUser || !in_array(strtolower(trim($currentUser->role ?? '')), $allowedRoles, true)) {
            abort(403, 'Unauthorized action.');
        }
    }

    private function loadUsers(): void
    {
        $authUserId = Auth::id();

        /** @var \Illuminate\Database\Eloquent\Collection<int, User> $allUsers */
        $allUsers = User::query()->orderBy('created_at', 'desc')->get();

        // Keep Auth User ALWAYS on 1st Position
        $authUserObj = $allUsers->firstWhere('id', $authUserId);
        $otherUsers = $allUsers->reject(fn($u) => $u->id === $authUserId)->values();

        $sortedList = collect();
        if ($authUserObj) {
            $sortedList->push($authUserObj);
        }

        $this->users = $sortedList->concat($otherUsers);
    }

    public function removeSelectedFile(): void
    {
        $this->excelFile = null;
    }

    public function saveUser(): void
    {
        $this->validate([
            'excelFile' => 'required|file|mimes:xlsx,xls,csv|max:10240',
        ], [
            'excelFile.required' => 'Please select an Excel file first.',
            'excelFile.mimes' => 'Only .xlsx, .xls, and .csv files are allowed.',
        ]);

        $filePath = $this->excelFile->getRealPath();
        $rows = [];

        if ($xlsx = SimpleXLSX::parse($filePath)) {
            $rows = $xlsx->rows();
        } elseif (($handle = fopen($filePath, 'r')) !== false) {
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                $rows[] = $data;
            }
            fclose($handle);
        }

        if (empty($rows) || count($rows) <= 1) {
            $this->dispatch('notify', ['message' => 'Excel file is empty or missing data rows.', 'type' => 'error']);
            return;
        }

        $header = array_map(fn($h) => strtolower(trim((string)$h)), array_shift($rows));

        $firstNameIdx = array_search('first_name', $header);
        $lastNameIdx = array_search('last_name', $header);
        $emailIdx = array_search('email', $header);
        $phoneIdx = array_search('phone_number', $header);
        $roleIdx = array_search('role', $header);
        $passwordIdx = array_search('password', $header);

        if ($firstNameIdx === false || $emailIdx === false) {
            $this->dispatch('notify', ['message' => 'Invalid Excel format. Required headers: first_name, email', 'type' => 'error']);
            return;
        }

        $importedCount = 0;

        foreach ($rows as $row) {
            $email = isset($row[$emailIdx]) ? trim((string)$row[$emailIdx]) : '';
            $firstName = isset($row[$firstNameIdx]) ? trim((string)$row[$firstNameIdx]) : '';

            if (empty($email) || empty($firstName)) {
                continue;
            }

            if (User::where('email', $email)->exists()) {
                continue;
            }

            $lastName = ($lastNameIdx !== false && isset($row[$lastNameIdx])) ? trim((string)$row[$lastNameIdx]) : '';
            $phone = ($phoneIdx !== false && isset($row[$phoneIdx])) ? trim((string)$row[$phoneIdx]) : '';
            $role = ($roleIdx !== false && isset($row[$roleIdx]) && !empty($row[$roleIdx])) ? strtolower(trim((string)$row[$roleIdx])) : 'employee';
            $plainPassword = ($passwordIdx !== false && isset($row[$passwordIdx]) && !empty($row[$passwordIdx])) ? trim((string)$row[$passwordIdx]) : 'Password@123';

            /** @var User $user */
            $user = User::create([
                'first_name' => $firstName,
                'last_name' => $lastName,
                'email' => $email,
                'phone_number' => $phone,
                'role' => $role,
                'status' => '1',
                'password' => Hash::make($plainPassword),
            ]);

            try {
                Mail::to($user->email)->send(new RegisterUserMail($user, $plainPassword));
            } catch (\Throwable $e) {
                // Ignore mail failure in batch loop
            }

            $importedCount++;
        }

        $this->excelFile = null;
        $this->loadUsers();

        if ($importedCount > 0) {
            $this->dispatch('notify', ['message' => "{$importedCount} Employee(s) imported successfully.", 'type' => 'success']);
        } else {
            $this->dispatch('notify', ['message' => 'No new employees were imported (Duplicate emails or empty rows).', 'type' => 'error']);
        }
    }

    public function editUser(int $userId): void
    {
        /** @var User|null $authUser */
        $authUser = Auth::user();

        if (! $authUser) {
            return;
        }

        $authUserId = (int) $authUser->id;
        $userRole = strtolower(trim($authUser->role ?? ''));

        $allowedRoles = ['super-admin', 'admin', 'hr', 'manager'];

        if (! in_array($userRole, $allowedRoles, true) && $authUserId !== $userId) {
            $this->dispatch('notify', ['message' => 'Aap sirf apna profile edit kar sakte hain.', 'type' => 'error']);
            return;
        }

        // Direct dispatch to UserDetailsModal listener
        $this->dispatch('edituser', id: $userId);
    }

    public function showUserDetails(int $userId): void
    {
        $this->selectedUser = User::with(['categories', 'groups'])->find($userId);
        $this->userDetailsModalOpen = true;
    }

    public function closeUserDetailsModal(): void
    {
        $this->userDetailsModalOpen = false;
        $this->selectedUser = null;
    }

    public function render(): mixed
    {
        return view('livewire.users', [
            'users' => $this->users,
        ]);
    }
}