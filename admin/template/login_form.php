<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Административная панель</title>

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f2f2f2;
            display: flex;
            justify-content: center; 
            align-items: center;
            height: 100vh;
            margin: 0;
            overflow-x: hidden;
            overflow-y: hidden;
        }

        form {
            position: relative;
            background-color: #fff;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            max-width: 300px;
            width: 100%;
            margin: 0 auto; 
        }



        input {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 12px;
            transition: border-color 0.3s;
        }

    input:focus {
      outline: none;
      box-shadow: 0 0 0 1.3px #adadade7;
      box-sizing: border-box; 
    }

        input[type="submit"] {
            background-color: #4caf50;
            color: #fff;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            color: #4caf50;
        }

        a:hover {
            text-decoration: underline;
        }

        .password-container {
            position: relative;
        }
        
       .error-box {
            display: none;
            color: white;
            background-color: #FF4100;
            padding: 10px;
            border-radius: 12px;
            position: absolute;
            top: 30%;
            left: 50%;
            transform: translateX(-50%);
            z-index: 1;
            width: 21%;
        } 

        .error-box.show {
            display: block;
        }
        
.container {
    text-align: center; 
}
        .show-password {
            position: absolute;
            right: 10px;
            bottom: 20px;
            cursor: pointer;
        }
        
    @media only screen and (max-width: 600px) {
    .error-box {
        top: 30%;
        width: 86%;
    }
}
    </style>
</head>
<body>


    <div class="container">
<div class="error-box <?php echo isset($resp['error-message']) ? 'show' : ''; ?>">
    <?php
        if (isset($resp['error-message'])) {
            echo $resp['error-message'];
        }
    ?>
</div>
<script>
    setTimeout(function() {
        document.querySelector('.error-box').classList.remove('show');
    }, 2500);
</script>

<form method="post" id="loginForm" onsubmit="return validateForm(event)">

    <div class="password-container">
        <input type="text" name="user" placeholder="Логин" required>
        <input type="password" name="pass" id="password" placeholder="Пароль" required>
        <span class="show-password" onclick="togglePassword()">
            <svg version="1.0" xmlns="https://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 1280 662" preserveAspectRatio="xMidYMid meet" fill="#808080">
                <g transform="translate(0.000000,662.000000) scale(0.100000,-0.100000)" fill="#808080" stroke="none">
                    <path d="M6330 6610 c-1399 -91 -2792 -594 -4189 -1515 -694 -457 -1415 -1050 -1957 -1609 l-183 -189 100 -108 c140 -151 583 -569 839 -794 1446 -1267 2965 -2053 4445 -2299 423 -70 660 -90 1105 -90 383 -1 517 7 845 49 1006 129 1985 482 2960 1068 876 526 1767 1287 2429 2075 l78 93 -19 22 c-11 12 -75 87 -144 167 -1111 1299 -2373 2239 -3644 2718 -576 216 -1111 340 -1725 398 -195 18 -747 26 -940 14z m421 -580 c562 -56 1096 -275 1534 -627 306 -246 561 -564 734 -916 91 -184 137 -304 187 -486 136 -496 123 -1033 -37 -1521 -81 -246 -179 -448 -324 -665 -109 -163 -193 -264 -349 -420 -232 -232 -450 -387 -751 -535 -280 -138 -550 -222 -875 -271 -196 -30 -580 -33 -775 -5 -680 94 -1246 378 -1705 852 -422 437 -671 963 -746 1574 -20 166 -15 517 11 680 159 1029 879 1869 1890 2205 218 72 403 111 655 138 80 9 455 6 551 -3z"/>
                    <path d="M6330 5359 c-375 -31 -742 -175 -1035 -404 -87 -68 -237 -217 -308 -306 -110 -136 -228 -347 -286 -512 -79 -225 -106 -402 -98 -657 7 -242 36 -385 119 -595 277 -703 983 -1174 1760 -1175 434 0 863 146 1203 412 80 62 242 226 310 313 182 232 307 512 359 804 l6 34 -42 -40 c-142 -130 -319 -224 -510 -270 -56 -14 -114 -18 -248 -18 -159 0 -184 3 -275 28 -381 104 -674 395 -767 763 -32 125 -32 371 0 486 51 185 144 348 274 478 90 90 171 148 285 204 140 69 261 101 426 113 l89 6 -68 44 c-347 219 -785 327 -1194 292z"/>
                </g>
            </svg>
        </span>
    </div>
    <input type="submit" value="Войти" name="loginForm">


</form>
</div>
    <script>
        function togglePassword() {
            var passwordField = document.getElementById("password");
            var type = passwordField.getAttribute("type");
            if (type === "password") {
                passwordField.setAttribute("type", "text");
            } else {
                passwordField.setAttribute("type", "password");
            }
        }

    </script>

</body>
</html>