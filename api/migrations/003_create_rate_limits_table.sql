CREATE TABLE IF NOT EXISTS rate_limits (
    id           SERIAL PRIMARY KEY,
    key          VARCHAR(255) NOT NULL,
    attempts     INTEGER      NOT NULL DEFAULT 1,
    window_start TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE INDEX IF NOT EXISTS idx_rate_limits_key ON rate_limits(key);
