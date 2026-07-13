<!DOCTYPE html>
<html lang="ja">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ログインフォーム</title>

    <!-- bootstrapのcssを読み込む用 -->
    <link href="../assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- 自分で作ったcssを読み込む用 -->
    <link rel="stylesheet" href="style.css">

    <!-- Googleフォントを使用 -->
    <link href="https://fonts.googleapis.com/css2?family=Zen+Kaku+Gothic+New:wght@500;700&display=swap" rel="stylesheet">
  </head>

  <body>
    <header class="bg-success text-white py-4">
      <div class="container-fiuld">
        <h5 class="md-0">株式会社コンドウソリューション</h5>
      </div>
    </header>

      <div class="container login-wrapper">
        <div class="row justify-content-center w-100"> 

          <div class="col-4">  
            <div class="m-5">
              <p class="text-muted small mb-4 text-center">KONDO SOLUTIONS — Groupware System</p>
              <h1>ログイン</h1>
            </div>
          
            <form action="login.php" method="post">

              <div class="my-4">
                <label for="employee_id" class="form-label" >社員番号：</label>    
                <input type="text" class="form-control" name="employee_id" id="employee_id" placeholder="社員番号を入力">
                <!-- 未入力エラーを表示 -->
                <?php
                if(isset($_GET['error'])) {
                  if($_GET['error'] === 'empty') {
                    echo '<p class ="text-danger">社員番号またはパスワードが未入力です。</p>';
                  } elseif($_GET['error'] === 'invalid') {
                    echo '<p class ="text-danger">社員番号またはパスワードに誤りがあります。</p>';
                  }
                }
                ?>
              </div>

              <div class="mb-5">
                <label for="password" class="form-label" >パスワード：</label>
                <input type="password" class="form-control" name="password" id="password" placeholder="パスワードを入力">
                <!-- 未入力エラーを表示 -->
                <?php
                if(isset($_GET['error'])) {
                  if($_GET['error'] === 'empty') {
                    echo '<p class ="text-danger">社員番号またはパスワードが未入力です。</p>';
                  } elseif($_GET['error'] === 'invalid') {
                    echo '<p class ="text-danger">社員番号またはパスワードに誤りがあります。</p>';
                  }
                }
                ?>
              </div>
              <button type="submit" class="btn btn-success btn-login">ログイン認証</button>
            </form>
          </div>
        </div>    
      </div>
  </body>
</html>