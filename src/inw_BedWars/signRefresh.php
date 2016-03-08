<?php

namespace inw_BedWars;

use pocketmine\scheduler\PluginTask;

class signRefresh extends PluginTask{

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($refreshTick){
        $this->plugin->refreshSign();
    }
}
