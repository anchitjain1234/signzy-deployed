<?php $this->assign('title', 'Sign Document'); ?>
<div class="container">
    <?php
    if (isset($unauthorized) && $unauthorized) {
        ?>
        <div id="alertdiv"></div>
        <div class="row">
            <h1 class="compid" id="<?php echo $company_info['Company']['id']; ?>">You are not authorized to sign on <?php echo $company_info['Company']['name'] ?>'s behalf.</h1>
            <h4 class="userid" id="<?php echo $userid; ?>">Click <a href="#" id="remind_heads">here</a> to resend emails to legal heads to make you authorized signatory.</h4>
        </div>
    </div>

    <?php
    echo $this->Html->script('jquery-1.11.1.min.js');
    echo $this->Html->script('bootstrap.min.js');
    echo $this->Html->script('signunauthorized.js');
    ?>

    <?php
} else {
    ?>
    <div class="row">
        <div class="col-md-12">
            <h3>Please Review and act on the document<?php
                if (isset($company_info)) {
                    echo " on behalf of <b><u>" . $company_info['Company']['name'] . "</u></b>";
                }
                ?></h3>
        </div>
    </div>

    <div class="row">
        <div class="col-md-1"><?php echo $this->Html->image('profile_new.png', array('alt' => 'Document')); ?></div>
        <div class="col-md-3"><h5><?php echo $name ?></h5><h5>Your Affiliated Company</h5><h6></h6></div>
        <div class="col-md-8 text-right">
            <?php
            $name_seperated = explode(".", $document['Document']['originalname']);
            $name_front = "";
            for ($i = 0; $i < count($name_seperated) - 1; $i++) {
                $name_front .= $name_seperated[$i];
            }
            ?>
            <?php
            $link = Router::url('/', true) . "documents/preview?name=" . $name_front . "&type=" . $name_seperated[count($name_seperated) - 1];
            $download_link = Router::url('/', true) . "documents/download?name=" . $name_front . "&type=" . $name_seperated[count($name_seperated) - 1];
            ?>
            <a href="<?php echo $download_link; ?>">Download</a> | <?php echo $this->Html->link('Trail', array('controller' => 'documents', 'action' => 'trail', $document['Document']['id'])); ?>
        </div>
    </div>

    <div class="row top-buffer">
        <div class="col-md-12 bg-highlight" style="height:400px;">
            <?php
            if (isset($document)) {

                echo "<iframe src='" . $link . "' width = '1100' height = '380'></iframe>";
            } else {
                echo "<div class=\"container\" >
                  <div class=\"row align-center\">
                    Document to be signed will appear here.
                  </div>
                </div>";
            }
            ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 text-center">
            <form class="form-inline">
                <div class="form-group">
                    <label>Biometric Signature</label>
                    <select class="form-control" id="biometric_type">
                        <!-- <option>Not required</option> -->
                        <option value="voicescan">Voicescan</option>
                        <option value="facescan">Facescan</option>
                    </select>
                </div>
                <button class="btn btn-primary" type="submit" id="sign">Scan</button>
            </form>
        </div>
    </div>

    <div class="row top-buffer">
        <div class="col-md-12 text-center">
            <button class="btn btn-success" type="submit" id="accept">Accept</button>
            <button class="btn btn-danger" type="submit" id="decline">Decline</button>
        </div>
    </div>

    <div class="modal fade" id="modal_voicescan" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Voicescan</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Place speak something on the mic, for voice scanning</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                    <button type="button" class="btn btn-primary">Save</button>
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
                    <button type="button" class="btn btn-primary">Capture</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_takesnap" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Face Snap</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-12">
                            <h5>Place your face in front of the camera</h5>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary">Send</button>
                </div>
            </div>
        </div>
    </div>


    <div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Decline Message</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-offset-2 col-md-8 text-center">
                            <textarea placeholder="Please add a message before declining..." class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-info" id="void_sign">Void</button>
                    <button type="button" class="btn btn-danger" id="decline_sign">Decline</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="modal_accept" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">Are you sure to sign the document?(You can revert back later)</h4>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-sm-offset-2 col-md-8 text-center">
                            <textarea placeholder="Feedback for document owner.." class="form-control"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-success" id="sure_success">Sure</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">Not Sure</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    echo $this->Html->script('jquery-1.11.1.min.js');
    echo $this->Html->script('bootstrap.min.js');
    echo $this->Html->script('jquery.webcam.min.js');
    echo $this->Html->script('sign.js');
    ?>
    <?php
}
?>
