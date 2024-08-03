<?php
// like_comment.php
$connect = new PDO('mysql:host=localhost;dbname=testingdb', 'root', '');

if (isset($_POST['comment_id'])) {
    $comment_id = $_POST['comment_id'];

    // Ambil jumlah likes saat ini
    $query = "SELECT likes FROM tbl_comment WHERE comment_id = :comment_id";
    $statement = $connect->prepare($query);
    $statement->bindParam(':comment_id', $comment_id);
    $statement->execute();
    $row = $statement->fetch();
    $current_likes = $row['likes'];

    // Tambahkan 1 like
    $current_likes++;

    // Perbarui jumlah likes di database
    $query = "UPDATE tbl_comment SET likes = :likes WHERE comment_id = :comment_id";
    $statement = $connect->prepare($query);
    $statement->bindParam(':likes', $current_likes);
    $statement->bindParam(':comment_id', $comment_id);
    $statement->execute();

    // Kembalikan jumlah likes terbaru
    echo $current_likes;
}
?>
