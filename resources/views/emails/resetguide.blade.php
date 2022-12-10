<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>{{$details['title']}}</title>
        <style>
            .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            }
            .header p {
            color: #28A802;
            margin-bottom: 20px;
            font-weight: bold;
            border-bottom: 1px solid #ddd;
            font-size: 24px;
            }
            .btn {
            background-color: #28A802;
            color: #fff;
            padding: 10px 20px;
            display:inline-block;
            border-radius: 5px;
            text-decoration: none;
            }
            .otp {
                text-align: center;
                padding: 10px;
                border: 1px solid #ddd;
            }

            .otp p {
                text-align: center;
                font-size: 24px;
                font-weight: bold;
            }

        </style>
    </head>
    <body>
      <div class="container">
        <div class="header">
            <p>Hirebalidriver HRM</p>
        </div>
        <strong>Hello {{$details['name']}}!</strong>
        <p>Atur ulang kata sandi Anda, masukkan kode otp pada aplikasi Anda.</p>

        <div class="otp"><p>{{$details['otp']}}</p></div>

        <p>Thank You,</p>
        <p>Hirebalidriver.com</p>
       </div>
    </body>
</html>
