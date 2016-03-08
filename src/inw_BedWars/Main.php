<?php

namespace inw_BedWars;

use pocketmine\block\Bed;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\SignChangeEvent;
use pocketmine\event\entity\EntityDeathEvent;
use pocketmine\event\entity\EntityLevelChangeEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\level\Level;
use pocketmine\level\Position;
use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\tile\Sign;
use pocketmine\utils\TextFormat;
use pocketmine\event\Listener;

class Main extends PluginBase implements Listener
{
    public $mode = 0;
    public $lobbyTick = 2400;
    public $gameTick = 12000;
    public $refreshTick = 20;

    public function onEnable(){
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info(TextFormat::GREEN . "BedWars Enabled!");
        $this->saveDefaultConfig();
        if ($this->getConfig()->get("maxplayers") !== 8) {
            $this->getLogger()->error("Max players on config must be 8!");
            $this->getServer()->shutdown();
        }else{
            $this->getServer()->getScheduler()->scheduleRepeatingTask(new signRefresh($this), 20);
        }
    }

    public function onDisable(){
        $this->getLogger()->info(TextFormat::RED . "BedWars Disabled!");
    }

    public function lobbyTime(){
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new lobbyTimer($this), 2400);
    }

    public function gameTime(){
        $this->getServer()->getScheduler()->scheduleRepeatingTask(new gameTimer($this), 12000);
    }

    public function endGame(){

    }

    public function signChange(SignChangeEvent $event){
        if ($event->getLine(0) === "[BedWars]") {
            $line1 = $event->getLine(1);
            if (!isset($line1)) {
                $event->getPlayer()->sendMessage(TextFormat::RED . "Please specify a valid arena name!");
            } else {
                foreach ($this->getConfig()->get("Arenas") as $arena => $g) {
                    if ($event->getLine(1) === $g["name"]) {
                        $sign = $event->getBlock();
                        if ($sign instanceof Sign) {
                            $pingame = count($this->getServer()->getLevelByName($g["name"])->getPlayers());
                            $sign->setText("[BedWars]", $g["world"], "$pingame / 8", $g["status"]);
                        }
                    }
                }
            }
        }
    }

    public function refreshSign(){
        foreach ($this->getConfig()->get("Arenas") as $arena => $g){
            foreach ($this->getServer()->getLevelByName($g["world"])->getTiles() as $tile) {
                if ($tile instanceof Sign) {
                    $pingame = count($this->getServer()->getLevelByName($g["name"])->getPlayers());
                    $tile->setText("[BedWars]", $g["world"], "$pingame / 8", $g["status"]);
                }
            }
        }
    }

    public function onBreak(BlockBreakEvent $event){
        if($event->getBlock() instanceof Bed){
            foreach($this->getConfig()->get("Arenas") as $arena => $g) {
                $bx = $event->getBlock()->getX();
                $by = $event->getBlock()->getY();
                $bz = $event->getBlock()->getZ();
                $blockpos = new Position($bx, $by, $bz);
                $bl = $event->getBlock()->getLevel()->getName();
                $bpos = $g["bluebed"];
                $rpos = $g["redbed"];
                $gpos = $g["greenbed"];
                $ypos = $g["yellowbed"];
                if($blockpos === $bpos and $bl === $g["world"]){
                    foreach($this->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                        $p->sendMessage(TextFormat::DARK_GRAY . "E' stato rotto il letto" .TextFormat::BLUE . "Blu" .TextFormat::DARK_GRAY . "! I giocatori blu sono stati eliminati!");
                    }
                }elseif($blockpos === $rpos and $bl === $g["world"]){
                    foreach($this->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                        $p->sendMessage(TextFormat::DARK_GRAY. "E' stato rotto il letto" .TextFormat::RED . "Rosso" .TextFormat::DARK_GRAY . "! I giocatori rossi sono stati eliminati!");
                    }
                }elseif($blockpos === $gpos and $bl === $g["world"]){
                    foreach($this->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                        $p->sendMessage(TextFormat::DARK_GRAY . "E' stato rotto il letto" .TextFormat::GREEN . "Verde" .TextFormat::DARK_GRAY . "! I giocatori verdi sono stati eliminati!");
                    }
                }elseif($blockpos === $ypos and $bl === $g["world"]){
                    foreach($this->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                        $p->sendMessage(TextFormat::DARK_GRAY . "E' stato rotto il letto" .TextFormat::YELLOW . "Giallo" .TextFormat::DARK_GRAY . "! I giocatori gialli sono stati eliminati!");
                    }
                }
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event){
        $block = $event->getBlock();
        if($block instanceof Sign){
            $text = $block->getText();
            foreach($this->getConfig()->get("Arenas") as $arena => $g) {
                if ($text[0] === "[BedWars]" and $text[1] === $g["name"]){
                    $event->getPlayer()->teleport($g["name"]);
                    $this->lobbyTime();
                    $this->getConfig()->set("status", "in-attesa");
                }
            }
        }
    }

    public function onDeath(EntityDeathEvent $event){
        $player = $event->getEntity();
        foreach($this->getConfig()->get("Arenas") as $arena => $g) {
            if($player->getLevel()->getName() === $g["world"]){
                foreach($this->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                    $name = $player->getName();
                    $p->sendMessage(TextFormat::DARK_GRAY . "$name è morto/a!");
                }
            }
        }
    }

    public function onTeleport(EntityLevelChangeEvent $event){
        $to = $event->getTarget();
        $player = $event->getEntity();
        foreach($this->getConfig()->get("Arenas") as $arena => $g){
            if($to instanceof Level and $player instanceof Player) {
                if($to->getName() === $g["world"] and $g["status"] === "in-gioco"){
                    $event->setCancelled(true);
                    $player->sendMessage(TextFormat::RED . "Partita già iniziata! Scegli un'altra arena!");
                }elseif($to->getName() === $g["name"] and count($this->getServer()->getLevelByName($g["name"])->getPlayers()) === 8 and $g["status"] === "pieno"){
                    $event->setCancelled(true);
                    $player->sendMessage(TextFormat::RED . "Arena piena! Scegli un'altra arena!");
                }elseif($to->getName() === $g["name"] and count($this->getServer()->getLevelByName($g["name"])->getPlayers()) < 8 and $g["status"] === "in-attesa"){
                    foreach($this->getServer()->getLevelByName($g["world"])->getPlayers() as $p){
                        $name = $player->getName();
                        $p->sendMessage(TextFormat::DARK_GRAY . "$name è entrato/a in partita!");
                    }
                }
            }
        }
    }
}
