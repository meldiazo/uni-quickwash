# Script de base de datos — Univalle QuickWash

Script compatible con SQLite. Incluye las tablas necesarias para usuarios, máquinas y reservas, además de las restricciones principales del sistema.

```sql
PRAGMA foreign_keys = ON;

DROP TABLE IF EXISTS reservations;
DROP TABLE IF EXISTS machines;
DROP TABLE IF EXISTS users;

CREATE TABLE users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(20) NOT NULL DEFAULT 'estudiante'
        CHECK (role IN ('estudiante', 'personal')),
    notifications_enabled BOOLEAN NOT NULL DEFAULT 0,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE machines (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME,
    updated_at DATETIME
);

CREATE TABLE reservations (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    user_id INTEGER NOT NULL,
    machine_id INTEGER NOT NULL,
    reservation_date DATE NOT NULL,
    schedule VARCHAR(50) NOT NULL,
    garment_count INTEGER NOT NULL CHECK (garment_count > 0),
    status VARCHAR(20) NOT NULL DEFAULT 'pendiente'
        CHECK (status IN (
            'pendiente',
            'en proceso',
            'finalizada',
            'cancelada'
        )),
    created_at DATETIME,
    updated_at DATETIME,

    FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE CASCADE,

    FOREIGN KEY (machine_id) REFERENCES machines(id)
        ON DELETE CASCADE
);

CREATE INDEX reservations_user_status_index
ON reservations(user_id, status);

CREATE INDEX reservations_date_schedule_status_index
ON reservations(reservation_date, schedule, status);

CREATE UNIQUE INDEX reservations_active_machine_slot_unique
ON reservations(machine_id, reservation_date, schedule)
WHERE status IN ('pendiente', 'en proceso');

INSERT INTO machines (name, created_at, updated_at) VALUES
('Máquina 1', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Máquina 2', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Máquina 3', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP),
('Máquina 4', CURRENT_TIMESTAMP, CURRENT_TIMESTAMP);
```

Los usuarios y las reservas de prueba se generan mediante `DatabaseSeeder`, usando:

```bash
php artisan migrate:fresh --seed
```
