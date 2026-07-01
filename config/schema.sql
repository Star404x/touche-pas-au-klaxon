-- Schéma de base de données pour TOUCHE PAS AU KLAXON
-- Compatible PostgreSQL, MySQL et SQLite

-- ============== TABLE UTILISATEURS ==============
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    firstname VARCHAR(255) NOT NULL,
    lastname VARCHAR(255) NOT NULL,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role VARCHAR(50) DEFAULT 'user',
    is_active BOOLEAN DEFAULT TRUE,
    avatar_url VARCHAR(500),
    bio TEXT,
    phone VARCHAR(20),
    rating DECIMAL(3,1) DEFAULT 5.0,
    last_login TIMESTAMP,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role),
    INDEX idx_is_active (is_active)
);

-- ============== TABLE AGENCES ==============
CREATE TABLE IF NOT EXISTS agences (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    slug VARCHAR(255) UNIQUE NOT NULL,
    siret VARCHAR(14) UNIQUE,
    description TEXT,
    email VARCHAR(255),
    phone VARCHAR(20),
    address VARCHAR(255),
    city VARCHAR(100),
    postal_code VARCHAR(10),
    country VARCHAR(100),
    website VARCHAR(500),
    logo_url VARCHAR(500),
    is_active BOOLEAN DEFAULT TRUE,
    rating DECIMAL(3,1) DEFAULT 5.0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_slug (slug),
    INDEX idx_is_active (is_active),
    INDEX idx_city (city)
);

-- ============== TABLE TRAJETS ==============
CREATE TABLE IF NOT EXISTS trajets (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    origin VARCHAR(255) NOT NULL,
    destination VARCHAR(255) NOT NULL,
    departure_time TIMESTAMP NOT NULL,
    driver_id INTEGER NOT NULL,
    agence_id INTEGER,
    passengers_count INTEGER DEFAULT 0,
    horns_count INTEGER DEFAULT 0,
    status VARCHAR(50) DEFAULT 'pending',
    estimated_duration INTEGER,
    started_at TIMESTAMP,
    ended_at TIMESTAMP,
    cancellation_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (driver_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (agence_id) REFERENCES agences(id) ON DELETE SET NULL,
    INDEX idx_driver (driver_id),
    INDEX idx_agence (agence_id),
    INDEX idx_status (status),
    INDEX idx_departure (departure_time)
);

-- ============== TABLE PASSAGERS ==============
CREATE TABLE IF NOT EXISTS trip_passengers (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    trajet_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    rating DECIMAL(3,1),
    comment TEXT,
    joined_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    left_at TIMESTAMP,
    FOREIGN KEY (trajet_id) REFERENCES trajets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY unique_passenger (trajet_id, user_id),
    INDEX idx_trajet (trajet_id),
    INDEX idx_user (user_id)
);

-- ============== TABLE KLAXONS ==============
CREATE TABLE IF NOT EXISTS horns (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    trajet_id INTEGER NOT NULL,
    user_id INTEGER NOT NULL,
    severity INTEGER DEFAULT 1,
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trajet_id) REFERENCES trajets(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trajet (trajet_id),
    INDEX idx_user (user_id),
    INDEX idx_created (created_at)
);

-- ============== TABLE NOTIFICATIONS ==============
CREATE TABLE IF NOT EXISTS notifications (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER NOT NULL,
    type VARCHAR(50) NOT NULL,
    message TEXT NOT NULL,
    data JSON,
    is_read BOOLEAN DEFAULT FALSE,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    read_at TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_is_read (is_read),
    INDEX idx_created (created_at)
);

-- ============== TABLE AVIS ==============
CREATE TABLE IF NOT EXISTS reviews (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    reviewer_id INTEGER NOT NULL,
    reviewee_id INTEGER NOT NULL,
    trajet_id INTEGER,
    rating INTEGER DEFAULT 5,
    comment TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (reviewer_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (reviewee_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (trajet_id) REFERENCES trajets(id) ON DELETE SET NULL,
    INDEX idx_reviewer (reviewer_id),
    INDEX idx_reviewee (reviewee_id),
    INDEX idx_trajet (trajet_id)
);

-- ============== TABLE LOGS ==============
CREATE TABLE IF NOT EXISTS activity_logs (
    id INTEGER PRIMARY KEY AUTO_INCREMENT,
    user_id INTEGER,
    action VARCHAR(100) NOT NULL,
    entity_type VARCHAR(50),
    entity_id INTEGER,
    old_values JSON,
    new_values JSON,
    ip_address VARCHAR(45),
    user_agent VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE SET NULL,
    INDEX idx_user (user_id),
    INDEX idx_action (action),
    INDEX idx_created (created_at)
);

-- ============== INDEXES SUPPLÉMENTAIRES ==============
CREATE INDEX idx_trajets_created ON trajets(created_at);
CREATE INDEX idx_users_created ON users(created_at);
CREATE INDEX idx_agences_created ON agences(created_at);

-- ============== DONNÉES DE TEST (optionnel) ==============
-- INSERT INTO users (firstname, lastname, email, password, role, is_active)
-- VALUES (
--     'Admin',
--     'Administrateur',
--     'admin@klaxon.local',
--     '$2y$12$...',  -- Remplacer par un vrai hash bcrypt
--     'admin',
--     TRUE
-- );
