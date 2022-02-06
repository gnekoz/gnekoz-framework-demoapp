<?php

namespace demo;

use gnekoz\rendering\SmartyRenderer as Super;

/**
 * TODO classe di workaround in attesa di implementare le invocazioni dirette
 * di controller dai template, oppure di nuova implementazione del framework
 *
 * @author gneko
 */
class SmartyRenderer extends Super
{
    public function render($data)
    {
        $auth = new Auth();
        $data['currentUser'] = $auth->getCurrentUser();
        return parent::render($data);
    }
}

?>
