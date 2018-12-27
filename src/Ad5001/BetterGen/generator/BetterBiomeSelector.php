<?php
/**
 *  ____             __     __                    ____                       
 * /\  _`\          /\ \__ /\ \__                /\  _`\                     
 * \ \ \L\ \     __ \ \ ,_\\ \ ,_\     __   _ __ \ \ \L\_\     __     ___    
 *  \ \  _ <'  /'__`\\ \ \/ \ \ \/   /'__`\/\`'__\\ \ \L_L   /'__`\ /' _ `\  
 *   \ \ \L\ \/\  __/ \ \ \_ \ \ \_ /\  __/\ \ \/  \ \ \/, \/\  __/ /\ \/\ \ 
 *    \ \____/\ \____\ \ \__\ \ \__\\ \____\\ \_\   \ \____/\ \____\\ \_\ \_\
 *     \/___/  \/____/  \/__/  \/__/ \/____/ \/_/    \/___/  \/____/ \/_/\/_/
 * Tomorrow's pocketmine generator.
 * @author Ad5001 <mail@ad5001.eu>, XenialDan <https://github.com/thebigsmileXD>
 * @link https://github.com/Ad5001/BetterGen
 * @category World Generator
 * @api 3.0.0
 * @version 1.1
 */
namespace Ad5001\BetterGen\generator;

use pocketmine\level\biome\Biome;
use pocketmine\level\generator\biome\BiomeSelector;
use pocketmine\level\generator\noise\Simplex;
use pocketmine\utils\Random;

class BetterBiomeSelector extends BiomeSelector {
	
	/** @var Biome */
	protected $fallback;
	
	/** @var Simplex */
	protected $temperature;
	/** @var Simplex */
	protected $rainfall;
	
	/** @var Biome[] */
	protected $biomes = [ ];

	/** @var callable */
	protected $lookup;

	/**
	 * Constructs the class
	 *
	 * @param Random $random
	 */
	public function __construct(Random $random) {
		parent::__construct($random);
		$this->temperature = new Simplex($random, 2, 1 / 16, 1 / 512);
		$this->rainfall = new Simplex($random, 2, 1 / 16, 1 / 512);
	}

	/**
	 * Inherited function
	 *
	 * @return void
	 */
	public function recalculate() {
	} // Using our own system, No need for that

	/**
	 * Adds a biome to the selector. Don't do this directly. Use BetterNormal::registerBiome
	 *
	 * @internal This method is called by BetterNormal::registerBiome
	 * @param Biome $biome
	 * @return void
	 */
	public function addBiome(Biome $biome) {
		$this->biomes[$biome->getId ()] = $biome;
	}

	/**
	 * Returns the temperature from a location
	 *
	 * @param int $x
	 * @param int $z
	 * @return float|int
	 */
	public function getTemperature($x, $z) {
		return ($this->temperature->noise2D($x, $z, true) + 1) / 2;
	}

	/**
	 * Returns the rainfall from a location
	 *
	 * @param int $x
	 * @param int $z
	 * @return float|int
	 */
	public function getRainfall($x, $z) {
		return ($this->rainfall->noise2D($x, $z, true) + 1) / 2;
	}
	
	/**
	 * Picks a biome relative to $x and $z
	 *
	 * @param int $x
	 * @param int $z
	 *        	
	 * @return Biome
	 */
	public function pickBiome($x, $z): Biome {
		$temperature = ($this->getTemperature($x, $z));
		$rainfall = ($this->getRainfall($x, $z));
		
		$biomeId = BetterNormal::getBiome($temperature, $rainfall);
		$b = (($biomeId instanceof Biome) ? $biomeId : ($this->biomes[$biomeId] ?? $this->fallback));
		return $b;
	}

	/**
	 * Lookup function called by recalculate() to determine the biome to use for this temperature and rainfall.
	 *
	 * @param float $temperature
	 * @param float $rainfall
	 *
	 * @return int biome ID 0-255
	 */
	protected function lookup(float $temperature, float $rainfall) : int{
		if($rainfall < 0.25){
			if($temperature < 0.7){
				return Biome::OCEAN;
			}elseif($temperature < 0.85){
				return Biome::RIVER;
			}else{
				return Biome::SWAMP;
			}
		}elseif($rainfall < 0.60){
			if($temperature < 0.25){
				return Biome::ICE_PLAINS;
			}elseif($temperature < 0.75){
				return Biome::PLAINS;
			}else{
				return Biome::DESERT;
			}
		}elseif($rainfall < 0.80){
			if($temperature < 0.25){
				return Biome::TAIGA;
			}elseif($temperature < 0.75){
				return Biome::FOREST;
			}else{
				return Biome::BIRCH_FOREST;
			}
		}else{
			if($rainfall < 0.25){
				return Biome::MOUNTAINS;
			}elseif($rainfall < 0.70){
				return Biome::SMALL_MOUNTAINS;
			}else{
				return Biome::RIVER;
			}
		}
	}
}