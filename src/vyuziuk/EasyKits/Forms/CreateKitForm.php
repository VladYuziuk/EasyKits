<?php
declare(strict_types=1);

namespace vyuziuk\EasyKits\Forms;

use AlexBrin\EasyForm\Elements\Input;
use AlexBrin\EasyForm\Elements\Slider;
use AlexBrin\EasyForm\Windows\CustomForm;
use pocketmine\Player;
use vyuziuk\EasyKits\Exceptions\CloneFoundException;
use vyuziuk\EasyKits\i18n\LanguageFactory;
use vyuziuk\EasyKits\KitManager;
use vyuziuk\EasyKits\Objects\Kit;
use vyuziuk\EasyKits\Provider\AsyncLibDatabase;

/**
 * Class CreateKitForm
 * @package vyuziuk\EasyKits\Forms
 *
 * @author  vyuziuk <vlad8yuziuk@gmail.com> <Telegram:@vyuziuk>
 * @version 1.0.0
 * @since   1.0.0
 */
class CreateKitForm extends CustomForm{
    public function __construct(){
        parent::__construct();
        $this->setTitle(LanguageFactory::getLanguage()->translateString("create.kit.form.title"));
        $this->addInput(new Input(LanguageFactory::getLanguage()->translateString("create.kit.form.input.name.text"), "", LanguageFactory::getLanguage()->translateString("create.kit.form.input.name.example")));
        $this->addInput(new Input(LanguageFactory::getLanguage()->translateString("create.kit.form.input.permission.text"), "", LanguageFactory::getLanguage()->translateString("create.kit.form.input.permission.example")));
        $this->addSlider(new Slider(LanguageFactory::getLanguage()->translateString("create.kit.form.slider.time"), 0, 24, 1, 0));
    }

    public function onSubmit(Player $player, $data): void{
        // key -> data
        // 0 -> name of the kit
        // 1 -> permission of the kit
        // 2 -> time
        // Todo: more settings
        $kit = new Kit($data[0], $data[1], intval($data[2]), $player->getInventory()->getContents(), $player->getArmorInventory()->getContents());
        try{
            KitManager::getInstance()->addKit($kit);
        }catch(CloneFoundException $exception){
        }
    }

    public function onCancel(Player $player): void{
        return;
    }
}