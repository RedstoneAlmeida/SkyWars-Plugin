<?php

namespace inw_BedWars;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class lobbyTimer extends PluginTask{

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($lobbyTick){
        if($lobbyTick === 2400){
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $p->sendMessage(TextFormat::DARK_GRAY . "La partita inizia fra 2 minuti!");
                }
            }
        }elseif($lobbyTick === 1200){
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $p->sendMessage(TextFormat::DARK_GRAY . "La partita inizia fra 1 minuto!");
                }
            }
        }else{
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                if($lobbyTick === 0 and count($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers()) === 8){
                    $this->plugin->gameTime();
                }else{
                    foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                        $p->sendMessage(TextFormat::RED . "Impossibile iniziare,non ci sono abbastanza player! Aspettate che qualcuno entri!");
                    }
                }
            }
        }
    }
}
