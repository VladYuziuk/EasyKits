<?php
declare(strict_types=1);

namespace vyuziuk\EasyKits\Objects;

use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\utils\Config;
use vyuziuk\EasyKits\Main;

/**
 * Class Kit
 * @package vyuziuk\EasyKits\Objects
 *
 * @author  vyuziuk <vlad8yuziuk@gmail.com> <Telegram:@vyuziuk>
 * @version 1.0.0
 * @since   1.0.0
 */
class Kit{
    /** @var string */
    private $name;
    /** @var string */
    private $permission;
    /** @var int */
    private $time;
    /** @var array */
    private $inventory = [];
    /** @var array  */
    private $armor_inventory = [];

    public function __construct(string $name, string $permission, int $time, array $inventory, array $armor_inventory){
        $this->name = $name;
        $this->permission = $permission;
        $this->time = $time;
        $this->inventory = $inventory;
        $this->armor_inventory = $armor_inventory;
    }

    public function toArray(): string{
        return json_encode([
            "name" => $this->name,
            "permission" => $this->permission,
            "time" => $this->time,
            "inventory" => $this->inventory,
            "armor_inventory" => $this->armor_inventory
        ]);
    }

    public function save(): void{
        $folder = Main::getInstance()->getDataFolder();

        $config = new Config($folder . "kitList.json", Config::JSON);
        $config->set($this->name, $this->toArray());
        $config->save();
    }

    /**
     * @return string
     */
    public function getName(): string{
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPermission(): string{
        return $this->permission;
    }

    /**
     * @return array
     */
    public function getInventory(): array{
        return $this->inventory;
    }

    /**
     * @return array
     */
    public function getArmorInventory(): array{
        return $this->armor_inventory;
    }

    /**
     * @return int
     */
    public function getTime(): int{
        return $this->time;
    }

    public static function fromArray(string $data): Kit{
        $kitData = json_decode($data, true);
        $inventoryItems = [];
        $armorInventoryItems = [];
        foreach($kitData["inventory"] as $itemData){
            $item = Item::jsonDeserialize($itemData);
            $inventoryItems[] = $item;
        }
        foreach($kitData["armor_inventory"] as $itemData){
            $item = Item::jsonDeserialize($itemData);
            $armorInventoryItems[] = $item;
        }
        return new Kit($kitData["name"], $kitData["permission"], $kitData["time"], $inventoryItems, $armorInventoryItems);
    }
}