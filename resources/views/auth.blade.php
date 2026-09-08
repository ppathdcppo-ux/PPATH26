<!DOCTYPE html>

<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>PPATH Portal</title>
<link rel="icon" href="/svg.png?v=3">

<style>
     body {
    margin: 0;
    font-family: Arial, sans-serif;
    background-color: #F8F6F0; 

    display: flex;
    justify-content: center;
    align-items: center;
    height: 100vh;
}

    .auth-card {
        width: 380px;
        background: white;
        padding: 25px;
        border-radius: 12px;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
    }

    .tab-group {
        display: flex;
        margin-bottom: 20px;
   
    }

    .tab {
        flex: 1;
        padding: 10px;
        border: none;
        cursor: pointer;
        background: #e5e7eb;
        font-weight: bold;
        transition: 0.3s;
    }

    .tab.active {
        background: #0E1A24;
        color: white;
    }

    .auth-form {
        display: flex;
        flex-direction: column;
    }

    .form-group {
        margin-bottom: 12px;
    }

    input {
        width: 100%;
        padding: 10px;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        outline: none;
        box-sizing: border-box;
    }

    input:focus {
        border-color: #4f46e5;
    }

    button[type="submit"] {
        padding: 10px;
        background: #0E1A24;
        color: white;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        font-weight: bold;
    }

    button[type="submit"]:hover {
        background: #2F4B63;
    }

    .hidden {
        display: none;
    }

    .success-box {
        background: #dcfce7;
        color: #166534;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 6px;
        text-align: center;
    }

    .error-box {
        background: #fee2e2;
        color: #991b1b;
        padding: 10px;
        margin-bottom: 15px;
        border-radius: 6px;
    }
</style>


</head>

<body>

<div class="auth-card">



 <div style="text-align:center;">
  <img src="/ppa1.png" alt="PPATH Logo" style="width:350px; height:auto;">
</div>


@if(session('success'))
    <div class="success-box">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="error-box">
        {{ session('error') }}
    </div>
@endif

@if($errors->any())
    <div class="error-box">
        <ul style="margin:0; padding-left:20px;">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="tab-group">
    <button type="button" onclick="switchMode('login')" id="tab-login" class="tab active">
        Log In
    </button>

    <button type="button" onclick="switchMode('signup')" id="tab-signup" class="tab">
        Sign Up
    </button>
</div>

<form method="POST" action="{{ route('auth.submit') }}" class="auth-form">
    @csrf

    <input type="hidden" name="auth_mode" id="auth-mode" value="login">

    <div id="field-name" class="form-group hidden">
        <input
            type="text"
            name="firstname"
            maxlength="50"
            placeholder="First Name">

        <br><br>

        <input
            type="text"
            name="lastname"
            maxlength="50"
            placeholder="Last Name">
    </div>

    <div class="form-group">
        <p>
            <b>Email Address</b>
        </p>

        <input
            type="email"
            name="email"
            required
            placeholder="name@gmail.com">
    </div>

    <div class="form-group">
        <p>
            <b>Password</b>
        </p>

        <input
            type="password"
            name="password"
            required
            minlength="8"
            placeholder="Minimum 8 characters">
    </div>

    <button type="submit" id="submit-btn">
        Log In
    </button>
</form>


</div>

<script>
function switchMode(mode) {

    document.getElementById('auth-mode').value = mode;

    const field = document.getElementById('field-name');
    const btn = document.getElementById('submit-btn');
    const login = document.getElementById('tab-login');
    const signup = document.getElementById('tab-signup');

    if (mode === 'login') {

        field.classList.add('hidden');

        btn.innerText = "Log In";

        login.classList.add('active');
        signup.classList.remove('active');

    } else {

        field.classList.remove('hidden');

        btn.innerText = "Create Account";

        login.classList.remove('active');
        signup.classList.add('active');
    }
}
</script>

</body>
</html>
