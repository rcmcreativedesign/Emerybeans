<?php
    //@$result = include 'setup.php';
    require_once '_globals.php';

    if ($loggedin) {
        require_once "classes/Database.php";
        require_once "classes/User.php";
        
        $database = new Database();
        $db = $database->getConnection();
        $user = new User($db);
        
        include_once 'classes/UserPreferance.php';

        $userPrefs = new UserPreferance($db);
        $page_size = $userPrefs->PageSize;

        $entry_count = getEntryCount($db);
        $num_pages = ceil($entry_count / $page_size);
        $current_page = isset($_GET["page"]) ? $_GET["page"] : 1;

        $db->close();
    }
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
                Clicking on an image now shows correctly in the pop-up.<br/>
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
                        echo "<li class=\"page-item" . ($i == $current_page ? " active" : "") . "\"><a class=\"page-link\" href=\"#\" data-page=\"{$i}\">{$i}</a></li>\n";
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

        <div id="image-popup" class="modal" tabindex="-1">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <img src="" id="imagePopup" style="width: 100%;"/>
                    </div>
                </div>
            </div>
        </div>
        <div id="edit-popup" data-entryid="" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="edit-popup-title" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered" role="document">
                <div class="model-content" style="background-color: white;">
                    <div class="model-header">
                        <h5 class="modal-title" id="edit-popup-title">Edit Caption</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group">
                                <label for="edit-popup-caption" class="col-form-label">Caption:</label>
                                <textarea id="edit-popup-caption"></textarea>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="edit-popup-save">Save Changes</button>
                    </div>
                </div>
            </div>
        </div>

        <script type="text/javascript">
            var currPage = <?php echo $current_page;?>;
            var totalPages = <?php echo $num_pages;?>;

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
                    var modal = $("#edit-popup");
                    modal.data("entryId", $(e.currentTarget).data("entryid"));
                    modal.data("caption", $(e.currentTarget).data("caption"));
                    modal.modal({backdrop: "static"});
                    modal.modal("show");
                });

                $("#edit-popup").on("show.bs.modal", function (event) {
                    $("#edit-popup-caption").val($(this).data("caption"));
                    $("#edit-popup-save").on("click", function () {
                        $.post("updateentry.php", { id: $(this).data("entryId"), caption: $("#edit-popup-caption").val() });
                        $("#edit-popup").modal("hide");
                    });
                });

                $("#edit-popup").on("hide.bs.modal", function (event) {
                    $("edit-popup-save").off();
                })

                $(".wrapper").on("click", ".page-link", function (e) {
                    e.preventDefault();
                    var $ct = $(e.currentTarget);
                    var page = $ct.data("page");
                    if (page && page != currPage) {
                        setCurrentPage(page);
                        getEntriesForPage(page);
                    }
                    else if ($ct.attr('id') == 'previous-page') {
                        if (!$ct.parent().hasClass('disabled')) {
                            currPage--;
                            setCurrentPage(currPage);
                            getEntriesForPage(currPage);
                        }
                    }
                    else if ($ct.attr('id') == 'next-page') {
                        if (!$ct.parent().hasClass('disabled')) {
                            currPage++;
                            setCurrentPage(currPage);
                            getEntriesForPage(currPage);
                        }
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

                        // Update paging
                        if (pagenumber == 1) {
                            pagingBack(true);
                            if (totalPages == 1)
                                pagingFoward(true);
                            else
                                pagingFoward(false);
                        }
                        else if (pagenumber == totalPages) {
                            pagingFoward(true);
                            if (totalPages == 1)
                                pagingBack(true);
                            else
                                pagingBack(false);
                        }
                        else {
                            pagingFoward(false);
                            pagingBack(false);
                        }
                        currPage = pagenumber;

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
                if (disable) {
                    $('#previous-page').parent().addClass('disabled');
                }
                else {
                    $('#previous-page').parent().removeClass('disabled');
                }
                
            }

            function pagingFoward(disable) {
                if (disable) {
                    $('#next-page').parent().addClass('disabled');
                }
                else {
                    $('#next-page').parent().removeClass('disabled');
                }

            }

            function setCurrentPage(page) {
                $('.page-item').removeClass('active');
                $('.page-link[data-page="' + page + '"]').parent().addClass('active');
            }
        </script>
        <?php } ?>

    </body>
</html>