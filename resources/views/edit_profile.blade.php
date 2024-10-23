<div id="modal-master" class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
        <div class="modal-header">
            <h5 class="modal-title" id="editProfileModalLabel">Edit Profil</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
        <form id="editProfileForm" method="POST">
            @csrf
            <div class="modal-body">
                <div class="form-group">
                    <label for="level_id">Level Pengguna</label>
                    <select name="level" id="editLevel" class="form-control" required>
                        <option value="">- Pilih Level -</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                    </select>
                    <small id="error-level_id" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="editUsername">Username</label>
                    <input type="text" class="form-control" id="editUsername" name="username" required>
                    <small id="error-username" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="editNama">Nama</label>
                    <input type="text" class="form-control" id="editNama" name="nama" required>
                    <small id="error-nama" class="error-text form-text text-danger"></small>
                </div>
                <div class="form-group">
                    <label for="editPassword">Password</label>
                    <input type="password" class="form-control" id="editPassword" name="password">
                    <small class="form-text text-muted">Kosongkan jika tidak ingin mengubah password</small>
                    <small id="error-password" class="error-text form-text text-danger"></small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-warning" data-dismiss="modal">Batal</button>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<script>
    // Ketika tombol Edit Profile diklik
    $('#editProfileBtn').on('click', function() {
        $.ajax({
            url: "{{ route('profile.edit', auth()->user()->id) }}",
            method: 'GET',
            success: function(response) {
                // Isi form dengan data yang diterima dari server
                $('#editUsername').val(response.user.username);
                $('#editNama').val(response.user.nama);
                $('#editLevel').val(response.user.level);
                // Tampilkan modal edit profil
                $('#modal-master').modal('show');
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });

    // Ketika form edit profile disubmit
    $('#editProfileForm').on('submit', function(e) {
        e.preventDefault();
        var formData = $(this).serialize();
        $.ajax({
            url: "{{ route('profile.update', auth()->user()->id) }}",
            method: 'POST',
            data: formData,
            success: function(response) {
                if (response.success) {
                    $('#modal-master').modal('hide');
                    // Reload halaman untuk memperbarui tampilan
                    location.reload();
                } else {
                    alert('Gagal memperbarui profil');
                }
            },
            error: function(xhr) {
                console.log(xhr.responseText);
            }
        });
    });
</script>
