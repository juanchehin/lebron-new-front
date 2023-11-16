<?php

class AdSense
{
    const caPub = "ca-pub-9722141461016863";

    public static function setAdsense($slot, $width = '100%', $height = null)
    {
        if ( !DEVELOPMENT )
        {
            $_width = "width:{$width}";
            $_height = $height ? "height:{$height}" : null;
            $style = "{$_width};{$_height}"
            ?>
            <ins class="adsbygoogle"
                 style="display:inline-block;<?= $style ?>"
                 data-ad-client="<?= self::caPub ?>"
                 data-ad-slot="<?= $slot ?>"
                <?php if ( !$height ) : ?>
                    data-ad-format="auto"
                <?php endif; ?>>
            </ins>
            <script>
                // (adsbygoogle = window.adsbygoogle || []).push({});
            </script>
            <?php
        }
    }

    public static function setAdsenseInFeed($slot)
    {
        ?>
        <ins class="adsbygoogle"
             style="display:inline-block;width:100%"
             data-ad-format="auto"
             data-ad-client="<?= self::caPub ?>"
             data-ad-slot="<?= $slot ?>"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        <?php

    }

    public static function setAdsArticle($slot)
    {
        ?>
        <ins class="adsbygoogle"
             style="display:block; text-align:center;"
             data-ad-layout="in-article"
             data-ad-format="fluid"
             data-ad-client="<?= self::caPub ?>"
             data-ad-slot="<?= $slot ?>"></ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({});
        </script>
        <?php
    }

    public static function setAutoAdsense($slot)
    {
        ?>
        <style>
            ins {
                min-width: 300px;
                min-height: 50px;
            }
        </style>
        <ins class="adsbygoogle"
             style="display:block;"
             data-ad-client="<?= self::caPub ?>"
             data-ad-slot="<?= $slot ?>"
             data-ad-format="auto"
             data-full-width-responsive="true">
        </ins>
        <script>
            (adsbygoogle = window.adsbygoogle || []).push({})
        </script>
        <?php
    }
}