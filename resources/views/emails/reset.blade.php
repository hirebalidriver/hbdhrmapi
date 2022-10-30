<!DOCTYPE html>
<html lang="en-US">
    <head>
        <meta charset="utf-8">
        <title>{{$details['title']}}</title>
        <style>
            .container {
                padding: 20px;
                background: #fff;
            }
            .btn {
                background-color: #2f3134;
                color: #fff;
                padding: 10px 20px;
                display:inline-block;
                border-radius: 5px;
                text-decoration: none;
            }
        </style>
    </head>
    <body>
      <div class="container">
        <strong>Hello {{$details['name']}}!</strong>
        <p>Atur ulang kata sandi Anda, klik link dibawah ini.</p>

        <a href="{{$details['link']}}" class="btn">Verify</a>

        <p>Thank You,</p>
        <p>Hirebalidriver.com</p>
       </div>
    </body>
</html>
