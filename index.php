<?php
    //@$result = include 'setup.php';
    require_once '_globals.php';
    require_once "classes/Database.php";
    require_once "classes/User.php";
    
    $database = new Database();
    $db = $database->getConnection();
    $user = new User($db);
    
    $link = $db;
    
    include_once 'classes/UserPreferance.php';

    $userPrefs = new UserPreferance($db);
    $page_size = $userPrefs->PageSize;

    $entry_count = getEntryCount($link);
    $num_pages = ceil($entry_count / $page_size);
    $current_page = isset($_GET["page"]) ? $_GET["page"] : 1;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Emery Beans</title>

        <link href="site.css" rel="stylesheet" type="text/css"/>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.5.0/font/bootstrap-icons.css">
        <script src="https://code.jquery.com/jquery-3.6.0.min.js" crossorigin="anontmous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
        <style>
            .entry-image { max-width: 300px; }
        </style>
    </head>
    <body>
        <div class="wrapper container">
            <?php include 'navbar.php'; ?>
            <h2>Welcome to Emery Beans!</h2>
            <?php if(!$loggedin) { ?>
            <p>Please login to use this site.</p>
            <?php } else { ?>
            <div class="alert alert-info">
                <b>What's New:</b><br/>
                You can &quot;like&quot; a picture by clicking the heart.<br/>
                You can change your display name and password in Accounts.<br/>
                You can click on a picture to see a bigger version, but it's not working right.<br/>
                You can click on a page number at the bottom to go to another page, but it's also not working right.<br/>
                Pictures you haven't seen yet have a red &quot;<span style="color: red;">new</span>&quot; next to them.
            </div>
            <div id="image-popup" class="modal" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <img src="" id="imagePopup"/>
                        </div>
                    </div>
                </div>
            </div>
            <div id="entries"></div>

            <nav aria-label="Page navigation" class="<?php echo $num_pages > 1 ? "" : "hidden";?>">
                <ul class="pagination">
                    <li class="page-item<?php echo $current_page == 1 ? " disabled" : "";?>">
                        <a id="previous-page" class="page-link" href="#" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                    </li>
                    <?php 
                    $i = 1;
                    while($i <= $num_pages && $i <= $num_pages) {
                        echo "<li class=\"page-item" . ($i == $current_page ? " active" : "") . "\"><a class=\"page-link\" href=\"#\" data-page=\"{$i}\">{$i}</a></li>";
                        $i++;
                    }?>
                    <li class="page-item disabled">
                        <a id="next-page" class="page-link" href="#" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                    </li>
                </ul>
            </nav>
            <?php include '_footer.php'; ?>
        </div>

        <script type="text/javascript">
            var currPage = <?php echo $current_page;?>;
            $(function () {
                getEntriesForPage(currPage);
                
                $(".wrapper").on("click", ".heart-icon", function(e) {
                    e.preventDefault();
                    var that = this;
                    if ($(that).find("i").hasClass("bi-heart")) {
                        var entryId = $(this).data("entryid");
                        $.post("likeentry.php", { id: entryId }, function (data) {
                            if (data) {
                                var response = JSON.parse(data);
                                if (response.success) {
                                    $(that).find("i").removeClass("bi-heart").addClass("bi-heart-fill");
                                    $(that).data("content", response.likedList);
                                    createLikedListPopover(that);
                                }
                            }
                        });
                    }
                });

                $(".wrapper").on("click", ".edit-icon", function (e) {
                    e.preventDefault();
                    var entryId = $(this).data("entryid");
                    alert("edit - " + entryId);
                });

                $(".wrapper").on("click", ".page-link", function (e) {
                    e.preventDefault();
                    var page = $(e.currentTarget).data("page");
                    if (page) {
                        getEntriesForPage(page);
                    }
                });

            });

            function createLikedListPopover(j) {
                var heart = $(j).find(".heart-icon");
                var contentData = heart.data("content");
                if (contentData) {
                    heart.attr("title", "Likes");
                    heart.popover({
                        html: true,
                        placement: "left",
                        trigger: "hover",
                        title: "Likes",
                        content: contentData
                    });
                }
            }

            function getEntriesForPage(pagenumber) {
                $("#entries").html("");
                $.get("getentries.php", { pagesize: <?php echo $page_size?>, page: pagenumber }, function (data) {
                    var dataArray = JSON.parse(data);
                    if (dataArray.length > 0) {
                        for (var i = 0;i < dataArray.length; i++) {
                            $("#entries").append("<div class=\"entry\" data-id=\"" + dataArray[i] + "\"></div>");
                        }
                        $(".entry").each(function (index) {
                            var that = this;
                            var entryId = $(that).data("id");
                            $.get("getentry.php", { id: entryId }, function (data) {
                                $(that).html(data);
                                createLikedListPopover(that);
                                $.get('getimage.php', { id: entryId }, function (data) {
                                    if(data) {
                                        var entryImage = document.getElementById('entryImage' + entryId);
                                        entryImage.src = data;
                                        $(entryImage).click(function () {
                                            var myModal = new bootstrap.Modal(document.getElementById("image-popup"));
                                            var modalImage = document.getElementById("imagePopup");
                                            modalImage.src = entryImage.src;
                                            myModal.show();
                                        });
                                    }
                                });
                            });
                        });
                    }
                });
            }

            function showImage(entryImage) {
                alert(entryImage.src.substr(1, 10));
            }

            function pagingBack(disable) {
                //if (disable) {}
                
            }

            function pagingFoward(disable) {

            }
        </script>
        <?php } ?>

    </body>
</html>
