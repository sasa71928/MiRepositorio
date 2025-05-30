<?php
// src/controllers/LogoutController.php

require_once __DIR__ . '/../helpers/auth.php';

function handleLogout(): void {
    logout_user();
}
