<!DOCTYPE html>
<html lang="ja">
    <head>
        <meta charset="UTF8">
        <title>mission_5-1</title>
        <h>mission_5-1</h><br>
    </head>
    <body>
        <?php
        
        
        // DB接続設定
        $dsn='データベース名=localhost';
        $user='ユーザー名';
        $password='パスワード';
        $pdo=new PDO($dsn,$user,$password,array(PDO::ATTR_ERRMODE=>PDO::ERRMODE_WARNING));
        //データベース内にテーブルを作成
        $sql = "CREATE TABLE IF NOT EXISTS tbshitest"
        ." ("
        . "id INT AUTO_INCREMENT PRIMARY KEY,"
        . "name char(32),"
        . "comment TEXT,"
        ."pass char(8),"
        ."date TEXT"
        .");";
        $stmt = $pdo->query($sql);
        //日時情報
        $date=date("Y年m月d日 H時i分s秒");
        //以下編集番号受け取り
        if(!empty($_POST["edit_num"])){
           //編集番号
            $edit_num=$_POST["edit_num"];
            //編集パスワード
            $edit_pass=$_POST["edit_pass"]; 
            //入力したデータレコードを抽出し、表示する
            $sql = 'SELECT * FROM tbshitest';
            $stmt = $pdo->query($sql);
            $results = $stmt->fetchAll();
            //テーブルの各行に以下の処理
            foreach($results as $row){
                //投稿番号と編集番号が一致＆パスワードも一致した場合の処理
                if($row["id"]==$edit_num&&$row["pass"]==$edit_pass){
                    $edit_name=$row["name"];
                    $edit_comment=$row["comment"];
                }
                    //投稿番号と編集番号が一致したがパスワードが一致しなかった場合「パスワードが違います」
                    if($row["id"]==$edit_num&&$row["pass"]!=$edit_pass){
                        echo "パスワードが違います"."<br>";
                    }
                }
        }
        //以下削除機能
        //削除番号受け取り
        if(!empty($_POST["delete"])){
            $delete=$_POST["delete"];
            //削除時のパスワード
            $delete_pass=$_POST["delete_pass"];
            //以下if(file_exists)に相当する文
            $sql='SELECT*FROM tbshitest';
            $stmt=$pdo->query($sql);
            $results=$stmt->fetchAll();
            //各行に以下の処理を行う
            foreach($results as $row){
            
                //投稿番号と削除番号が一致＆パスワードも位置する場合該当箇所に「削除済み」と記載
                    if($row["id"]==$delete&&$row["pass"]==$delete_pass){
                        echo "削除済み"."<br>";
                        $id = $delete;
                        $sql = 'delete from tbshitest where id=:id';
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
                        $stmt->execute();
                        

                    }
                    //投稿番号と削除番号が一致したがパスワードが一致しなかった場合「パスワードが違います」とブラウザに表記
                    if($row["id"]==$delete&&$row["pass"]!=$delete_pass){
                        //ブラウザ表示
                        echo "パスワードが違います"."<br>";
            }            
                    }
               
        }
        
        //以下編集機能
        //新規投稿機能（名前、コメントを受け取る）
        if(!empty($_POST["name"])&&!empty($_POST["comment"])){
            //パスワードを受け取っていた場合名前、コメント、日時を受け取る
            if(!empty($_POST["pass"])){
                $name=$_POST["name"];
                $comment=$_POST["comment"];
                $pass=$_POST["pass"];
                //編集タグが使用された場合の処理(パスワード機能の中に組み込む)
                if(!empty($_POST["edit_tag"])){
                    //編集タグ受け取り
                    $edit_tag=$_POST["edit_tag"];
                    //以下if(file_exists)に相当する文
                    $sql="SELECT*FROM tbshitest";
                    $stmt=$pdo->query($sql);
                    $results=$stmt->fetchAll();
                        //ファイルの各行に対して以下の処理を行う
                        foreach($results as $row){
                            //投稿番号が編集タグと一致する＆パスワードも一致する場合、その行を新たに送信した内容に書き換える
                            if($row["id"]==$edit_tag){
                                $id = $edit_tag; //変更する投稿番号
                                $name=$_POST["name"];
                                $comment=$_POST["comment"];
                                $sql='UPDATE tbshitest SET name=:name,comment=:comment,pass=:pass,date=:date WHERE id=:id';
                                $stmt = $pdo->prepare($sql);
                                $stmt->bindParam(':id',$id,PDO::PARAM_STR);
                                $stmt->bindParam(':name', $name, PDO::PARAM_STR);
                                $stmt->bindParam(':comment', $comment, PDO::PARAM_STR);
                                $stmt->bindParam(':pass', $pass, PDO::PARAM_STR);
                                $stmt->bindPAram(':date',$date,PDO::PARAM_STR);
                                $stmt->execute();
                            }
                            
                            
                        }
                //編集タグが使用されなかった場合新規投稿扱いにする
                }else{
                    //以下新規投稿
                    $sql = $pdo -> prepare("INSERT INTO tbshitest (name, comment,pass,date) VALUES (:name, :comment,:pass,:date)");
                    $sql -> bindParam(':name', $name, PDO::PARAM_STR);
                    $sql -> bindParam(':comment', $comment, PDO::PARAM_STR);
                    $sql -> bindParam(":pass",$pass,PDO::PARAM_STR);
                    $sql -> bindParam(":date",$date,PDO::PARAM_STR);
                    $sql -> execute();
                    
                    
                    
                }
            //パスワードが送信されなかった場合「エラーです。パスワードを記入してください」と表示    
            }else{
                echo "エラーです。パスワードを記入してください"."<br>";
            }
        }    
        
        //ブラウザ表示
        $sql = 'SELECT * FROM tbshitest';
        $stmt = $pdo->query($sql);
        $results = $stmt->fetchAll();
        foreach ($results as $row){
            //$rowの中にはテーブルのカラム名が入る
            echo $row['id'].',';
            echo $row['name'].',';
            echo $row['comment'];
            echo $row["date"]."<br>";
        echo "<hr>";
        }
        ?>
        <!--フォームの上に一直線を表示-->
        <hr width="100%" align="center">
        <!--新規投稿のフォーム-->
        <form action=""method="post">
            <input type="text" name="name" value="<?php if(!empty($edit_name)){echo $edit_name;}?>" placeholder="名前">
            <input type="text" name="comment" value="<?php if(!empty($edit_comment)){echo $edit_comment;}?>" placeholder="コメント"><br>
            <input type="text" name="pass" value="<?php if(!empty($edit_pass)){echo $edit_pass;}?>" placeholder="パスワード"><br>
            <input type="submit" name="send"><br>
            <input type="hidden" name="edit_tag" value="<?php if(!empty($edit_num)){echo $edit_num;}?>">
        </form>
        <!--削除及び編集フォーム-->
        <form action=""method="post">
            <!--以下削除フォーム-->
            <input type="number" name="delete" placeholder="削除対象番号"><br>
            <input type="text" name="delete_pass"placeholder="パスワード"><br>
            <input type="submit" name="send_delete" value="削除"><br>
            <!--以下編集フォーム-->
            <input type="number" name="edit_num" placeholder="編集対象番号"><br>
            <input type="text" name="edit_pass" placeholder="パスワード"><br>
            <input type="submit" name="send_edit" value="編集">
        </form>
    </body>
</html>