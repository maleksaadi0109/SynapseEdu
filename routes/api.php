<?php

/*
|--------------------------------------------------------------------------
| Modular API Routes
|--------------------------------------------------------------------------
|
| Your API routes are split into clean domain files:
| - routes/auth.php     -> Authentication & Session
| - routes/user.php     -> User Profiles & Passwords
| - routes/course.php   -> Courses & Lessons
|
*/

require __DIR__.'/auth.php';
require __DIR__.'/user.php';
require __DIR__.'/course.php';
