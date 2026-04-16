@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Data Pengguna</h3>
            <div class="chart-legend">
                <x-atoms.button onclick="openModal('createUserModal')">
                    <x-atoms.icon name="plus" style="width: 16px; height: 16px; margin-right: 8px;" />
                    Tambah Pengguna
                </x-atoms.button>
            </div>
        </div>
        <div class="card-body">
            @php
                $tableColumns = [
                    ['data' => 'id', 'name' => 'id', 'title' => 'ID', 'width' => '50px'],
                    ['data' => 'name', 'name' => 'name', 'title' => 'Nama Lengkap'],
                    ['data' => 'email', 'name' => 'email', 'title' => 'Email'],
                    ['data' => 'role', 'name' => 'role', 'title' => 'Role'],
                    ['data' => 'created_at', 'name' => 'created_at', 'title' => 'Tanggal Dibuat', 'searchable' => false],
                    ['data' => 'action', 'name' => 'action', 'title' => 'Aksi', 'orderable' => false, 'searchable' => false],
                ];
            @endphp

            <x-organisms.datatables 
                id="usersTable" 
                url="{{ route('users.data') }}" 
                :columns="$tableColumns" 
            />
        </div>
    </div>
</div>

<x-organisms.modal 
    id="createUserModal" 
    title="Tambah Pengguna Baru" 
    description="Masukkan informasi dasar untuk membuat akun pengguna baru."
>
    <form action="{{ route('users.store') }}" method="POST">
        @csrf
        
        <div class="form-group">
            <x-atoms.label for="name" value="Nama Lengkap" />
            <x-atoms.input type="text" name="name" id="name" placeholder="Contoh: John Doe" required />
        </div>

        <div class="form-group">
            <x-atoms.label for="email" value="Alamat Email" />
            <x-atoms.input type="email" name="email" id="email" placeholder="john@example.com" required />
        </div>

        <div class="form-group">
            <x-atoms.label for="password" value="Password" />
            <x-atoms.input type="password" name="password" id="password" required />
        </div>

        <div class="form-group">
            <x-atoms.label for="role" value="Role Akses" />
            <x-atoms.select name="role" id="role" required>
                <option value="user">User</option>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
            </x-atoms.select>
        </div>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('createUserModal')">
                Batal
            </x-atoms.button>
            <x-atoms.button variant="primary" type="submit">
                Simpan Pengguna
            </x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>

<x-organisms.modal 
    id="editUserModal" 
    title="Edit Role Pengguna" 
    description="Ubah hak akses role untuk pengguna ini."
>
    <form id="formEditRole" method="POST" action="">
        @csrf
        @method('PUT')
        
        <div class="form-group">
            <x-atoms.label for="editUserName" value="Nama Pengguna" />
            <x-atoms.input type="text" id="editUserName" name="name"  />
        </div>

        <div class="form-group">
            <x-atoms.label for="editUserRole" value="Role Akses" />
            <x-atoms.select name="role" id="editUserRole" required>
                <option value="user">User</option>
                <option value="manager">Manager</option>
                <option value="admin">Admin</option>
            </x-atoms.select>
        </div>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('editUserModal')">
                Batal
            </x-atoms.button>
            <x-atoms.button variant="primary" type="submit">
                <x-atoms.icon name="check" style="width: 15px; height: 15px; margin-right: 6px;" />
                Simpan Perubahan
            </x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>

<x-organisms.modal 
    id="deleteUserModal" 
    title="Hapus Pengguna" 
    description="Tindakan ini tidak dapat dibatalkan."
>
    <form id="formDeleteUser" method="POST" action="">
        @csrf
        @method('DELETE')
        
        <div style="margin-bottom: 20px;">
            <p>Apakah Anda yakin ingin menghapus pengguna <strong id="deleteUserNameText"></strong>?</p>
        </div>

        <x-slot name="footer">
            <x-atoms.button variant="secondary" type="button" onclick="closeModal('deleteUserModal')">
                Batal
            </x-atoms.button>
            <x-atoms.button variant="danger" type="submit">
                <x-atoms.icon name="trash" style="width: 15px; height: 15px; margin-right: 6px;" />
                Ya, Hapus
            </x-atoms.button>
        </x-slot>
    </form>
</x-organisms.modal>
@endsection

@push('scripts')
<script>
    function openModal(modalId) {
        document.getElementById(modalId).classList.add('active');
        document.body.style.overflow = 'hidden';
    }

    function closeModal(modalId) {
        document.getElementById(modalId).classList.remove('active');
        document.body.style.overflow = '';
    }

    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal-overlay')) {
            event.target.classList.remove('active');
            document.body.style.overflow = '';
        }
    });

    document.addEventListener('DOMContentLoaded', function() {
        
        $('#usersTable').on('click', '.btn-edit', function(e) {
            e.preventDefault();
            
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            const userRole = $(this).data('role');
            
            const form = document.getElementById('formEditRole');
            
            form.action = `/users/${userId}`;
            
            document.getElementById('editUserName').value = userName;
            document.getElementById('editUserRole').value = userRole;
            
            openModal('editUserModal');
        });
        $('#usersTable').on('click', '.btn-delete', function(e) {
            e.preventDefault();
            
            const userId = $(this).data('id');
            const userName = $(this).data('name');
            
            const form = document.getElementById('formDeleteUser');
            form.action = `/users/${userId}`;
            document.getElementById('deleteUserNameText').innerText = userName;
            
            openModal('deleteUserModal');
        });
    });
</script>
@endpush