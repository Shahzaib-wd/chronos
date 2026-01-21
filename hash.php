<?php

$password = "ChangeMe123!";

// Generate bcrypt hash
$hash = password_hash($password, PASSWORD_BCRYPT);

// Output the hash
echo $hash;
