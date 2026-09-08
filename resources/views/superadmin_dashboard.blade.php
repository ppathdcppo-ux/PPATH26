
<!DOCTYPE html>
<html>
<head>
    <title>Super Admin Dashboard</title>
    <link rel="icon" href="/svg.png?v=3">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
    
    body {
        font-family: Arial, sans-serif;
        background-image: url("svg.png");
        background-size:43%;
        


   background-position: center 70px;

    
    background-repeat: no-repeat;

    
    background-attachment: fixed;
    }

    table {
        border-collapse: collapse;
    }

    th, td {
        padding: 12px;
        text-align: left;
        vertical-align: middle;
    }

    thead tr {
        background: #f3f4f6;
    }

    tbody tr:hover {
        background: #f9fafb;
    }

    .action-btn {
        display: inline-block;
        padding: 6px 10px;
        margin-right: 5px;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        text-decoration: none;
    }

    .approve {
        background: #22c55e;
        color: white;
    }

    .reject {
        background: #ef4444;
        color: white;
    }

    .delete {
        background: #dc2626;
        color: white;
    }

    .section-title {
        margin-top: 30px;
        margin-bottom: 10px;
    }

    .card {
        background: white;
        border-radius: 10px;
        padding: 20px;
        box-shadow: 0 2px 8px rgba(0,0,0,0.05);
    }
</style>
</head>

    

<body class="bg-white-100 pt-20">
<!-- Fixed Header -->
<header class="fixed top-0 left-0 w-full shadow-lg z-50"
        style="background-color: #0E1A24;">

    <div class="max-w-7xl mx-auto px-6 py-2 flex justify-between items-center">

        <div>
            <h1 class="text-white text-2xl font-bold">
                PPATH Super Admin Dashboard
            </h1>
            <p class="text-blue-100 text-sm">
                Manage administrator accounts and permissions
            </p>
        </div>

        <div class="flex gap-3">
            

         <a href="/logout"
   class="text-white px-4 py-2 rounded font-bold"
   style="background-color: #FF2400;">
    Logout
</a>
        </div>

    </div>

</header>
<div class="max-w-6xl mx-auto mt-5 p-6 pb-32rounded shadow bg-black/20 backdrop-blur-sm border border-white/100">

   <div class="flex justify-between items-center mb-4">

    <h2 class="text-xl font-bold">
       <div class="grid grid-cols-3 gap-2 mb-4">
    <div class="text-white p-2 rounded-lg text-center"
     style="background-color: #FF2400;">
        <h3 class="text-sm">Pending</h3>
        <p class="text-xl font-bold">
            {{ count($pendingAdmins) }}
        </p>
    </div>

    <div class="text-white p-2 rounded-lg text-center"
     style="background-color: #0E4C92;">
        <h3 class="text-sm">Active Accounts</h3>
        <p class="text-xl font-bold">
            {{ count($approvedAdmins) }}
        </p>
    </div>

  
</div>
        Pending Admin Accounts
    </h2>

    <div class="flex gap-2">

        <!-- Refresh -->
       <button
    onclick="location.reload();"
    class="text-white px-4 py-2 rounded"
    style="background-color: #2F4B63;">
    Reload
</button>

        
    </div>

</div>

    @if(session('error'))
        <p class="text-red-600 mb-3">{{ session('error') }}</p>
    @endif

    <table class="w-full table-fixed border">
        <thead>
            <tr style="background-color: #D8DEE4;">
                <th class="p-2 text-center">Name</th>
                <th class="p-2 text-center">Email</th>
                <th class="p-2 text-center">Action</th>
            </tr>
        </thead>

        <tbody>
            @foreach($pendingAdmins as $admin)
                <tr class="border-b">
                    <td class="p-6   text-center font-bold">
                        {{ $admin->firstname }} {{ $admin->lastname }}
                    </td>

                    <td class="p-2 text-center font-bold">
                        {{ $admin->email }}
                    </td>

                    <td class="p-2 text-center">
    <div class="flex justify-center gap-2">

        <!-- APPROVE -->
        <a href="/admin-approve/{{ $admin->id }}"
   class="text-white px-3 py-1 rounded"
   style="background-color: #2F4B63;">
    Approve
</a>

        <!-- REJECT -->
        <a href="/admin-reject/{{ $admin->id }}"
           class=" text-white px-3 py-1 rounded"
           style="background-color: #FF2400;">
            Reject
        </a>

    </div>
</td>
                </tr>
            @endforeach
        </tbody>
    </table>
<br><br>
<hr style="border: 5px solid #000000;">
<br>
<h2 class="text-xl font-bold mb-4">
    Active Admin Accounts
</h2>

<table class="w-full table-fixed border">
    <thead>
      <tr style="background-color: #D8DEE4;">
            <th class="p-2 text-center">Name</th>
           <th class="p-2 text-center">Email</th>
<th class="p-2 text-center">Action</th>
        </tr>
    </thead>

    <tbody>
        @foreach($approvedAdmins as $admin)
            <tr class="border-b ">
                <td class="p-6 text-center">
    <div class="flex items-center justify-center gap-2">

        @if(\Illuminate\Support\Facades\Cache::has('admin-online-' . $admin->id))
       <span class="w-2 h-2 rounded-full shadow-md" style="background-color:#00ff5e;"></span>
        @else
            <span class="w-2 h-2 bg-gray-400 rounded-full"></span>
        @endif

        <span class="font-bold">
            {{ $admin->firstname }} {{ $admin->lastname }}
        </span>

    </div>
</td>
<td class="p-2 text-center font-bold">
    {{ $admin->email }}
</td>

<td class="p-2 text-center">
   <div class="flex justify-center gap-2 items-start">

    <button
        type="button"
        onclick="openEditModal(
            '{{ $admin->id }}',
            '{{ $admin->firstname }}',
            '{{ $admin->lastname }}',
            '{{ $admin->email }}',
            '{{ $admin->role }}'
        )"
        class="text-white px-3 py-1 rounded"
        style="background-color:#0E4C92;">
        Edit User
    </button>

    <a href="javascript:void(0)"
       class="text-white px-3 py-1 rounded"
       style="background-color:#FF2400;"
       onclick="confirmDeleteAdmin('/delete-admin/{{ $admin->id }}')">
        Delete
    </a>

</div>
    <!-- HIDDEN RESET FORM -->
  
</td>
               
            </tr>
            
        @endforeach
    </tbody>
</table>
</div>
<div id="editModal"
     class="fixed inset-0 bg-black/50 hidden items-center justify-center z-50">

    <div class="bg-white rounded-lg w-[500px] p-6">

        <h2 class="text-2xl font-bold mb-4">
            Edit User
        </h2>

        <form method="POST" action="/update-admin">

            @csrf

            <input
                type="hidden"
                name="id"
                id="edit_id">

            <div class="mb-3">

                <label>First Name</label>

                <input
                    id="edit_firstname"
                    name="firstname"
                    class="border w-full p-2 rounded">

            </div>

            <div class="mb-3">

                <label>Last Name</label>

                <input
                    id="edit_lastname"
                    name="lastname"
                    class="border w-full p-2 rounded">

            </div>

            <div class="mb-3">

                <label>Email</label>

                <input
                    id="edit_email"
                    name="email"
                    class="border w-full p-2 rounded">

            </div>

            <div class="mb-3">

                <label>Role</label>

<select
    id="edit_role"
    name="role"
    class="border w-full p-2 rounded">

                    <option value="admin">
                        Admin
                    </option>

                    <option value="super_admin">
                        Super Admin
                    </option>

                </select>

            </div>

            <div class="mb-4">

                <label>New Password</label>

                <input
                    type="password"
                    name="password"
                    class="border w-full p-2 rounded"
                    placeholder="Leave blank to keep current password">

                <small class="text-gray-500">
                    Leave blank if you don't want to change the password.
                </small>

            </div>

            <div class="flex justify-end gap-2">

                <button
                    type="button"
                    onclick="closeEditModal()"
                    class="bg-gray-400 text-white px-4 py-2 rounded">

                    Cancel

                </button>

                <button
                    type="submit"
                    class="text-white px-4 py-2 rounded"
                    style="background:#0E4C92;">

                    Save Changes

                </button>

            </div>

        </form>

    </div>

</div>
</body>
<script>
    function confirmDeleteAdmin(url) {

    Swal.fire({
        title: 'Delete Admin?',
        text: 'This admin account will be permanently deleted.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#CB0000',
        cancelButtonColor: '#6B7280',
        confirmButtonText: 'Delete',
        cancelButtonText: 'Cancel'
    }).then((result) => {

        if (result.isConfirmed) {
            window.location.href = url;
        }

    });

}

</script>
<script>
function openEditModal(id, firstname, lastname, email, role)
{
    document.getElementById('edit_id').value = id;
    document.getElementById('edit_firstname').value = firstname;
    document.getElementById('edit_lastname').value = lastname;
    document.getElementById('edit_email').value = email;
    document.getElementById('edit_role').value = role;

    document.getElementById('editModal').classList.remove('hidden');
    document.getElementById('editModal').classList.add('flex');
}

function closeEditModal()
{
    document.getElementById('editModal').classList.remove('flex');

    document.getElementById('editModal').classList.add('hidden');
}
</script>
</html>
