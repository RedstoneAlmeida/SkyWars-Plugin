<?php

namespace inw_BedWars;

use pocketmine\scheduler\PluginTask;
use pocketmine\utils\TextFormat;

class gameTimer extends PluginTask{

    public function __construct(Main $plugin){
        $this->plugin = $plugin;
        parent::__construct($plugin);
    }

    public function onRun($gameTick){
        if($gameTick === 12000){
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $p->sendMessage(TextFormat::DARK_GRAY . "Partita iniziata! Tempo: 10 minuti");
                }
            }
        }elseif($gameTick === 6000){
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $p->sendMessage(TextFormat::DARK_GRAY . "La partita termina in 5 minuti!");
                }
            }
        }elseif($gameTick === 4800){
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $p->sendMessage(TextFormat::DARK_GRAY . "La partita termina in 4 minuti!");
                }
            }
        }elseif($gameTick === 3600){
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $p->sendMessage(TextFormat::DARK_GRAY . "La partita termina in 3 minuti!");
                }
            }
        }elseif($gameTick === 2400){
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $p->sendMessage(TextFormat::DARK_GRAY . "La partita termina in 2 minuti!");
                }
            }
        }elseif($gameTick === 1200){
            foreach($this->plugin->getConfig()->get("Arenas") as $arena => $g){
                foreach($this->plugin->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $p->sendMessage(TextFormat::DARK_GRAY . "La partita termina in 1 minuto!");
                }
            }
        }
    }
}
