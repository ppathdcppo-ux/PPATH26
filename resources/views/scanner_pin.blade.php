<!DOCTYPE html>
<html lang="en">
<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Scanner Access</title>

<style>

*{
margin:0;
padding:0;
box-sizing:border-box;
font-family:Segoe UI,sans-serif;
}

body{

background:#f4f6f9;

display:flex;
justify-content:center;
align-items:center;

height:100vh;

}

.card{

width:100%;
max-width:400px;

background:white;

padding:30px;

border-radius:12px;

box-shadow:0 10px 30px rgba(0,0,0,.08);

}

h2{

text-align:center;

color:#1f4e79;

margin-bottom:20px;

}

input{

width:100%;

padding:12px;

border:1px solid #ccc;

border-radius:8px;

font-size:16px;

}

button{

width:100%;

padding:12px;

margin-top:20px;

background:#1f4e79;

color:white;

border:none;

border-radius:8px;

cursor:pointer;

font-size:16px;

}

button:hover{

background:#163a5c;

}

.error{

margin-top:15px;

color:red;

text-align:center;

}

</style>

</head>

<body>

<div class="card">

<h2>Scanner Access</h2>

<form method="POST" action="{{ url('/scanner-pin') }}">

@csrf

<input
type="password"
name="pin"
placeholder="Enter Scanner PIN"
required>

<button>

Continue

</button>

</form>

@if(session('error'))

<div class="error">

{{ session('error') }}

</div>

@endif

</div>

</body>
</html>
