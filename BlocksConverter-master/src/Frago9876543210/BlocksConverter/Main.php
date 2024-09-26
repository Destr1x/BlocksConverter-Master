<?php

declare(strict_types=1);

namespace Frago9876543210\BlocksConverter;

use pocketmine\block\BlockLegacyIds;
use pocketmine\command\CommandSender;
use pocketmine\playend\Command;
use pocketmine\commar\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\world\World;
use pocketmine\world\format\Chunk;
use pocketmine\world\format\SubChunk;
use pocketmine\utils\TextFormat;



class Main extends PluginBase implements Blocks{
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool{
        if($sender instanceof Player){
            $world = $sender->getWorld(); // Получение мира игрока в PMMP 5.x
            foreach($world->getLoadedChunks() as $chunkX => $chunkZ){
                $chunk = $world->getChunk($chunkX, $chunkZ); // Получаем чанк
                if($chunk instanceof Chunk){
                    foreach($chunk->getSubChunks() as $subChunkY => $subChunk){
                        if($subChunk instanceof SubChunk){
                            for($x = 0; $x < 16; $x++){
                                for($y = 0; $y < 16; $y++){
                                    for($z = 0; $z < 16; $z++){
                                        $blockId = $subChunk->getFullBlock($x, $y, $z) >> 4; // Получаем ID блока
                                        if(isset(Blocks::IDS[$blockId])){
                                            $newBlockId = Blocks::IDS[$blockId];
                                            // Установка нового блока
                                            $subChunk->setFullBlock($x, $y, $z, ($newBlockId << 4) | $subChunk->getBlockData($x, $y, $z));
                                        }
                                    }
                                }
                            }
                        }
                    }
                    // Помечаем чанк как измененный
                    $world->setChunk($chunkX, $chunkZ, $chunk);
                    $sender->sendMessage(TextFormat::AQUA . "Modified chunk " . $chunkX . " " . $chunkZ);
                }
            }
            // Сохранение мира
            $world->save(true);
            $sender->sendMessage(TextFormat::GREEN . "World \"" . $world->getFolderName() . "\" has been saved!");

            sleep(3);
            // Ожидание и перезапуск сервера
            $this->getServer()->shutdown();
        }
        return true;
    }
}
