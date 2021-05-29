<?php

declare(strict_types=1);

namespace NgLamVN\AntiToolBox;

use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

class Loader extends PluginBase implements Listener
{
    public Config $config;

    public function onEnable()
    {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->saveResource("config.yml");
        $this->config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
    }

    /**
     * @param DataPacketReceiveEvent $event
     * @priority NORMAL
     * @ignoreCancelled TRUE
     */
    public function onRecieve (DataPacketReceiveEvent $event)
    {
        $player = $event->getPlayer();
        $packet = $event->getPacket();

        if ($packet instanceof LoginPacket)
        {
            $deviceOS = (int)$packet->clientData["DeviceOS"];
            $deviceModel = (string)$packet->clientData["DeviceModel"];

            if ($deviceOS !== 1) //AndroidOS
            {
                return;
            }
            if ($packet->clientId == 0) //Invalid ClientID
            {
                $player->close("", $this->config->get("kick-message"));
                return;
            }

            /**
             * Something about device model check, for example:
             * Original client: XIAOMI Note 8 Pro
             * Toolbox client: Xiaomi Note 8 Pro
             *
             * For another Example
             * Original client: SAMSUNG SM-A105F
             * Toolbox client: samsung SM-A105F
             */

            $name = explode(" ", $deviceModel);
            if (!isset($name[0]))
            {
                return;
            }
            $check = $name[0];
            $check = strtoupper($check);
            if ($check !== $name[0])
            {
                $player->close("", $this->config->get("kick-message"));
            }
        }
    }
}