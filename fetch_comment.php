<?php
// fetch_comment.php
ini_set('memory_limit', '512M');

$connect = new PDO('mysql:host=localhost;dbname=testingdb', 'root', '');

// Hitung jumlah total komentar
$queryTotal = "SELECT COUNT(*) as total FROM tbl_comment WHERE parent_comment_id = '0'";
$statementTotal = $connect->prepare($queryTotal);
$statementTotal->execute();
$totalComments = $statementTotal->fetchColumn();

// Tentukan jumlah komentar per halaman
$commentsPerPage = 25;

// Hitung jumlah total halaman
$totalPages = ceil($totalComments / $commentsPerPage);

// Tentukan halaman saat ini
$currentPage = isset($_GET['page']) ? $_GET['page'] : 1;

// Hitung offset (mulai dari mana komentar akan ditampilkan)
$offset = ($currentPage - 1) * $commentsPerPage;

// Ambil komentar untuk halaman saat ini
$query = "
    SELECT * FROM tbl_comment 
    WHERE parent_comment_id = '0' 
    ORDER BY comment_id DESC
    LIMIT $offset, $commentsPerPage
";

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();

$output = '';
foreach ($result as $row) {
    $timestamp = strtotime($row["date"]);
    $waktu = '';

    // Hitung selisih waktu
    $selisih = time() - $timestamp;

    // Ubah ke format yang lebih mudah dibaca
    if ($selisih < 60) {
        $waktu = 'Baru saja';
    } elseif ($selisih < 3600) {
        $waktu = round($selisih / 60) . ' menit yang lalu';
    } elseif ($selisih < 86400) {
        $waktu = round($selisih / 3600) . ' jam yang lalu';
    } else {
        $waktu = round($selisih / 86400) . ' hari yang lalu';
    }

    $output .= '
        <div class="card mb-3">
            <div class="card-body">
                <div class="d-flex flex-start">
                    <div class="flex-grow-1">
                        <p class="mt-0 text-dark">' . $row["comment_sender_name"] . ' <small>(' . $waktu . ')</small></p>
                        <small class="text-dark">' . $row["comment"] . '</small>
                        <div class="d-flex justify-content-between align-items-center">
                            <button type="button" class="btn btn-outline-danger btn-sm love-button" data-comment-id="' . $row["comment_id"] . '">
                                <small><i class="fas fa-heart"></i></small> <small><span class="love-count">' . $row["likes"] . '</span></small>
                            </button>
                            <button type="button" class="btn btn-dark btn-sm reply" data-comment-id="' . $row["comment_id"] . '">Balas</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    ';

    $output .= get_reply_comment($connect, $row["comment_id"], 48);
}

echo $output;

function get_reply_comment($connect, $parent_id = 0, $marginleft = 0)
{
    $query = "
        SELECT * FROM tbl_comment WHERE parent_comment_id = :parent_id
    ";
    $output = '';
    $statement = $connect->prepare($query);
    $statement->execute([':parent_id' => $parent_id]);
    $result = $statement->fetchAll();
    $count = $statement->rowCount();
    if ($count > 0) {
        foreach ($result as $row) {
            $timestamp = strtotime($row["date"]);
            $waktu = '';

            // Hitung selisih waktu
            $selisih = time() - $timestamp;

            // Ubah ke format yang lebih mudah dibaca
            if ($selisih < 60) {
                $waktu = 'Baru saja';
            } elseif ($selisih < 3600) {
                $waktu = round($selisih / 60) . ' menit yang lalu';
            } elseif ($selisih < 86400) {
                $waktu = round($selisih / 3600) . ' jam yang lalu';
            } else {
                $waktu = round($selisih / 86400) . ' hari yang lalu';
            }

            $output .= '
                <div class="card mb-3" style="margin-left: ' . $marginleft . 'px;">
                    <div class="card-body">
                        <div class="d-flex flex-start">
                            <div class="flex-grow-1">
                                <p class="mt-0 text-dark">' . $row["comment_sender_name"] . ' <small>(' . $waktu . ')</small></p>
                                <small class="text-dark">' . $row["comment"] . '</small>
                                <div class="d-flex justify-content-between align-items-center">
                                    <button type="button" class="btn btn-outline-danger btn-sm love-button" data-comment-id="' . $row["comment_id"] . '">
                                        <small><i class="fas fa-heart"></i></small> <small><span class="love-count">' . $row["likes"] . '</span></small>
                                    </button>
                                    <button type="button" class="btn btn-dark btn-sm reply" data-comment-id="' . $row["comment_id"] . '">Balas</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ';

            $output .= get_reply_comment($connect, $row["comment_id"], $marginleft + 10);
        }
    }
    return $output;
}
