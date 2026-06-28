CREATE TABLE IF NOT EXISTS carts (
    id         SERIAL PRIMARY KEY,
    user_id    INT NOT NULL REFERENCES users(id) ON DELETE CASCADE,
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (user_id)
);

CREATE TABLE IF NOT EXISTS cart_items (
    id         SERIAL PRIMARY KEY,
    cart_id    INT  NOT NULL REFERENCES carts(id) ON DELETE CASCADE,
    product_id INT  NOT NULL REFERENCES products(id) ON DELETE CASCADE,
    quantity   INT  NOT NULL DEFAULT 1 CHECK (quantity > 0),
    created_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    updated_at TIMESTAMPTZ NOT NULL DEFAULT NOW(),
    UNIQUE (cart_id, product_id)
);
