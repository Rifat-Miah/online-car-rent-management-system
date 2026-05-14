<!DOCTYPE html>
<html>
<head>
    <title>Online Car Rent Home</title>

    <style>
        *{
            margin:0;
            padding:0;
            font-family: Arial, sans-serif;
        }

        body{
            height:100vh;
            display:flex;
            justify-content:center;
            align-items:center;
            background:url('homepazeImg.png') no-repeat center center/cover;
        }
        body::before{
            content:"";
            position:absolute;
            width:100%;
            height:100%;
            background:rgba(49, 49, 49, 0.6);
        }

        .container{
            position:relative;
            z-index:1;
            width:350px;
            padding:40px;
            text-align:center;

            background:rgba(255,255,255,0.1);
            backdrop-filter:blur(6px);

            border-radius:12px;
            color:white;
        }

        h1{
            margin-bottom:10px;
            font-size:32px;
        }

        p{
            margin-bottom:25px;
            font-size:15px;
            color:#ddd;
        }

        .btn{
            display:block;
            text-decoration:none;
            margin:12px 0;
            padding:12px;
            border-radius:6px;
            color:white;
            font-weight:bold;
            transition:0.3s;
        }

        .admin{
            background:red;
        }

        .user{
            background:green;
        }

        .signup{
            background:blue;
        }
     
        .footer{
            margin-top:20px;
            font-size:13px;
            color:#ddd;
        }

    </style>
</head>

<body>

    <div class="container">

        <h1>Online Car Rent</h1>

        <p>Welcome to our car rental management system</p>

        <a href="" class="btn admin">Admin Login</a>

        <a href="" class="btn user">User Login</a>

        <a href="" class="btn signup">Sign Up</a>

        <div class="footer">
            Rent a Car in Dhaka
        </div>

    </div>

</body>
</html>