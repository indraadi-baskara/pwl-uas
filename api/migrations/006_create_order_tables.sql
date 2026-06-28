CREATE TABLE IF NOT EXISTS orders (
    id         SERIAL PRIMARY KEY,
    user_id    INT  NOT NULL REFERENCES users(id) ON DELETE RESTRICT,
    status     TEXT NOT NULL DEFAULT 'pending',
    total      INT  NOT NULL DEFAULT 0,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    CONSTRAINT orders_status_check
        CHECK (status IN ('pending','processing','shipped','completed','cancelled'))
);

-- Snapshot product name + price at order time so history is stable even if product changes.
CREATE TABLE IF NOT EXISTS order_items (
    id         SERIAL PRIMARY KEY,
    order_id   INT  NOT NULL REFERENCES orders(id) ON DELETE CASCADE,
    product_id INT  REFERENCES products(id) ON DELETE SET NULL,
    name       TEXT NOT NULL,
    price      INT  NOT NULL,
    quantity   INT  NOT NULL CHECK (quantity > 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW()
);
