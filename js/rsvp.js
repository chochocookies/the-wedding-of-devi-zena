function ambilNamaDariURL() {
    const urlParams = new URLSearchParams(window.location.search);
    const nama = urlParams.get("to");
    return nama;
}

function gantiTeks() {
    const nama = ambilNamaDariURL();
    if (nama) {
        document.getElementById("nama").textContent = nama;
    }
}

window.addEventListener("load", gantiTeks);

lightbox.option({
    'resizeDuration': 200,
    'wrapAround': true,
    'disableScrolling': true
});
document.addEventListener('DOMContentLoaded', function () {
    var http = new XMLHttpRequest();
    http.open("GET", "https://mycloud.devazy.iotflows.com/onload?reload=sukses", true);
    http.send();
    http.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            // alert(this.responseText);
            var data = JSON.parse(this.responseText);
            var len = data.length;
            for (var i = 0; i < len; i++) {
                $("#message_list").prepend('<li>\n' +
                    '                        <span class="from">' + data[i].nama + '</span>\n' +
                    '                        <span class="guest_message">' + data[i].pesan + '</span>\n' +
                    '                    </li>');
            }
        }
    }
    var splide = new Splide('.splide',
        {
            type: 'loop',
            perPage: 3,
            pagination: true,
            arrows: true,
            lazyLoad: 'nearby',
            perMove: 1,
            breakpoints: {
                640: {
                    perPage: 2,
                },
                480: {
                    arrows: false,
                    perPage: 1,
                    focus: 'center',
                }
            }
        });
    splide.mount();
});

$(document).ready(function () {
    $("#btn_message").on("click", function (e) {
        e.preventDefault();
        if ($("#guest_name").val() === '') {
            return alert('Nama Harus Diisi')
        }
        if ($("#message").val() === '') {
            return alert('Pesan Harus Diisi');
        }
        if ($("#guest_name").val() !== '' && $("#message").val() !== '') {
            $(this).html('Mengirim Pesan...');
            var btn = $(this);
            btn.attr("disabled", "disabled");
            setTimeout(function () {
                $('#form_message').submit();
            }, 1500);
        }
    });

    $('#form_message').on('submit', function (event) {
        event.preventDefault();
        var btn = $("#btn_message");
        if (true) {
            btn.html('<small>Terima kasih atas doa dan ucapannya</small>');
            setTimeout(function () {
                btn.html('Kirim');
            }, 3000);
            var http = new XMLHttpRequest();
            http.open("GET", "https://mycloud.devazy.iotflows.com/button?nama=" + $('#guest_name').val() + "&pesan=" + $('#message').val(), true);
            http.send();
            $("#message_list").prepend('<li>\n' +
                '                        <span class="from">' + $('#guest_name').val() + '</span>\n' +
                '                        <span class="guest_message">' + $('#message').val() + '</span>\n' +
                '                    </li>');
            $('#guest_name').val('');
            $('#message').val('');
        } else {

        }
    })
});

function alert(msg) {
    alertify.alert().set(
        {
            title: 'Informasi',
            transition: 'slide',
            message: msg,
            movable: true,
            closable: false
        }).show();
}

$(document).ready(function() {
    // Simpan data pengguna yang sudah memberikan like
    var likedComments = JSON.parse(localStorage.getItem('likedComments')) || [];

    // Tambahkan event listener pada tombol love
    $(".love-button").click(function() {
        var commentId = $(this).data("comment-id");
        var $loveButton = $(this);
        var $loveCount = $loveButton.find(".love-count");

        // Tambahkan like
        var currentLoveCount = parseInt($loveCount.text());
        $loveCount.text(currentLoveCount + 1);

        // Simpan data pengguna yang sudah memberikan like
        likedComments.push(commentId);
        localStorage.setItem('likedComments', JSON.stringify(likedComments));

        // Ubah warna tombol menjadi merah
        $loveButton.addClass("btn-danger").removeClass("btn-outline-danger");
    });
});
$(document).ready(function() {
    // Simpan data pengguna yang sudah memberikan like
    var likedComments = JSON.parse(localStorage.getItem('likedComments')) || [];

    // Fungsi untuk memuat komentar
    function loadComments(page) {
        $.ajax({
            url: 'fetch_comment.php',
            type: 'GET',
            data: { page: page },
            success: function(response) {
                $('#message_list').html(response);

                // Perbarui jumlah like untuk setiap komentar
                $(".love-count").each(function() {
                    var commentId = $(this).parents(".love-button").data("comment-id");
                    var likeCount = likedComments.filter(id => id === commentId).length;
                    $(this).text(likeCount);
                });

                // Tambahkan event listener pada tombol love setelah memuat komentar
                $(".love-button").click(function() {
                    var commentId = $(this).data("comment-id");
                    var $loveButton = $(this);
                    var $loveCount = $loveButton.find(".love-count");

                    // Tambahkan like
                    var currentLoveCount = parseInt($loveCount.text());
                    $loveCount.text(currentLoveCount + 1);

                    // Simpan data pengguna yang sudah memberikan like
                    likedComments.push(commentId);
                    localStorage.setItem('likedComments', JSON.stringify(likedComments));

                    // Ubah warna tombol menjadi merah
                    $loveButton.addClass("btn-danger").removeClass("btn-outline-danger");
                });
            },
            error: function() {
                alert('Terjadi kesalahan saat memuat komentar.');
            }
        });
    }

    // Panggil fungsi untuk memuat komentar pada halaman pertama
    loadComments(1);

    // Tambahkan event listener pada link pagination
    $(document).on('click', '.pagination a', function(e) {
        e.preventDefault();
        var page = $(this).attr('href').split('=')[1];
        loadComments(page);
    });
});



