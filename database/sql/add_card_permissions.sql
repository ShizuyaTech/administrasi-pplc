-- SQL untuk menambahkan permissions kartu e-money
-- Jalankan query ini di database untuk menambahkan permissions

INSERT INTO permissions (name, description, created_at, updated_at) VALUES
('view-card', 'View kartu e-money', NOW(), NOW()),
('create-card', 'Create kartu e-money', NOW(), NOW()),
('edit-card', 'Edit kartu e-money', NOW(), NOW()),
('delete-card', 'Delete kartu e-money', NOW(), NOW());

-- Optional: Berikan akses penuh ke Super Admin
-- Sesuaikan role_id dengan ID role Super Admin di database Anda
-- INSERT INTO permission_role (permission_id, role_id) 
-- SELECT id, 1 FROM permissions WHERE name LIKE '%card%';
