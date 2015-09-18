<?php $this->assign('title', 'Your Profile'); ?>

<?php

function change_status_number_to_status($status) {
    if ($status === "0") {
        return "<span class=\"label label-warning\">Pending</span>";
    } elseif ($status === "1") {
        return "<span class=\"label label-success\">Completed</span>";
    } elseif ($status === "2") {
        return "<span class=\"label label-default\">Void</span>";
    } else {
        return "<span class=\"label label-danger\">Disputed</span>";
    }
}
?>

<div class="container">
    <div class="row">
    </div>
    <div class="row">
    </div>
    <div class="row">
    </div>
    <div class="row">
        <div class="col-md-2" id="profilepicture">
            <?php if(isset($profile) && $profile!=[])
            {
                $link = Router::url(array('controller'=>'profile','action'=>'preview',$profile['Profile']['profilepicture']), true);
                echo "<img src='".$link."' alt='Profile Picture' width='160' height='150'>";
            }
            else
            {
                echo $this->Html->image('profile.jpg', array('alt' => 'Profile Image', 'width'=>'160','height'=>'150'));
            }
             ?>
            <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#modal_facescan" id="update_picture">
                Update Profile Picture
            </button>
        </div>
        <div class="col-md-10">
            <div class="row">
                <div class="col-md-12">
                    <div><h1 style="display:inline-block;"><?php echo $name; ?></h1>
                        <?php
                        if (isset($profile['Profile']['verified']) && $profile['Profile']['verified'] === Configure::read('profile_verified')) {
                            echo "<span class=\"label label-success\">Verified</span></div>";
                        } else {
                            echo "<span class=\"label label-danger\">Not Verified</span></div>";
                            echo $this->Html->link('Click to verify profile',array('controller'=>'profile','action'=>'verification'),array('class'=>'btn btn-success pull-right'));
                        }
                        ?>

                        <span>Your description</span>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <label>Authorized Signatory: </label>
                        <span class="label label-info">Company 1</span>
                        <span class="label label-info">Company 2</span>
                        <span class="label label-info">Company 3</span>
                        <span class="label label-info">Company 4</span>
                    </div>
                </div>
            </div>
        </div>
        <br/>
        <div class="row">
            <div class="col-md-6" >
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Recent Uploads</h3>
                    </div>
                    <table class="table">
                        <?php
                        foreach ($uploads as $upload):
                            echo "<tr><td>";
                            //$url="#";
                            $url = Router::url('/', true) . "documents/show/" . $upload['Document']['id'];
                            echo "<a href=\"" . $url . "\">";
                            echo $upload['Document']['name'];
                            echo "</a></td><td></td><td>";
                            $status = change_status_number_to_status($upload['Document']['status']);
                            echo $status;
                            echo "</td>";
                        endforeach;
                        ?>
                        <tr>
                            <td colspan="3"><?php echo $this->Html->link('View all >>', array('controller' => 'documents')); ?></td>
                        </tr>
                    </table>
                </div>
            </div>

            <div class="col-md-6" >
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <h3 class="panel-title">Disputes</h3>
                    </div>
                    <table class="table">
                        <?php
                        foreach ($disputeduploads as $upload):
                            echo "<tr><td>";
                            //$url="#";
                            $url = Router::url('/', true) . "documents/show/" . $upload['Document']['id'];
                            echo "<a href=\"" . $url . "\">";
                            echo $upload['Document']['name'];
                            echo "</a></td><td></td><td>";
                            $status = change_status_number_to_status($upload['Document']['status']);
                            echo $status;
                            echo "</td>";
                        endforeach;
                        ?>
                    </table>
                </div>
            </div>


        </div>
    </div>

    <div class="modal fade" id="modal_facescan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close close_facescan" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Facescan</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-offset-4">
                            <button id="startcamera" class="btn btn-success">Start camera</button>
                            <button id="stopcamera" class="btn btn-danger">Stop camera</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <h5>Place your face in front of the camera</h5>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="camera">
                                <video id="video">Video stream not available.</video> 
                            </div>
                        </div>
                        <canvas id="canvas" style="display: none;">
                        </canvas>

                    </div>
                    <div class="row">
                        <div class="col-md-2 col-md-offset-5">
                            <button id="startbutton" class="btn btn-default">Capture Image</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-8 col-md-offset-2">
                            <div class="output">
                                <img id="photo" alt="The screen capture will appear in this box."> 
                            </div>
                        </div>
                    </div>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default close_facescan" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary" id="submit_new_picture" disabled="disabled">Submit new picture</button>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
echo $this->Html->script('jquery-1.11.1.min.js');
echo $this->Html->script('bootstrap.min.js');
echo $this->Html->script('profileindex.js');
?>