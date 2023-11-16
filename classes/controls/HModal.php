<?php
/**
 * Created by PhpStorm.
 * User: iCDB
 * Date: 06/04/2019
 * Time: 16:00
 */

class HModal
{
    public function drawModal()
    {
        ?>
        <style>
            .overlay {
                position: absolute;
                top: 0;
                bottom: 0;
                left: 0;
                right: 0;
                background: rgba(0, 0, 0, 0.5);
                transition: opacity 200ms;
                visibility: hidden;
                opacity: 0;

            .light {
                background: rgba(255, 255, 255, 0.5);
                border-color: #aaa;
                box-shadow: 0 2px 10px rgba(0, 0, 0, 0.25)
            }

            .cancel {
                position: absolute;
                width: 100%;
                height: 100%;
                cursor: default;
            }

            .cancel:target {
                visibility: visible;
                opacity: 1;

            }

            .popup {
                margin: 75px auto;
                padding: 20px;
                background: #fff;
                border: 1px solid #666;
                width: 300px;
                box-shadow: 0 0 50px rgba(0, 0, 0, 0.5);
                position: relative;

            h2 {
                margin-top: 0;
                color: #666;
                font-family: "Trebuchet MS", Tahoma, Arial, sans-serif;
            }

            .close {
                position: absolute;
                width: 20px;
                height: 20px;
                top: 20px;
                right: 20px;
                opacity: 0.8;
                transition: all 200ms;
                font-size: 24px;
                font-weight: bold;
                text-decoration: none;
                color: #666;

            .close:hover {
                opacity: 1;
            }

            .content {
                max-height: 400px;
                overflow: auto;
            }

            p {
                margin: 0 0 1em;

            p:last-child {
                margin: 0;
            }

        </style>
        <div id="popup2" class="overlay light">
            <a class="cancel" href="#"></a>
            <div class="popup">
                <h2>What the what?</h2>
                <div class="content">
                    <p>Click outside the popup to close.</p>
                </div>
            </div>
        </div>
        <?php
    }
}