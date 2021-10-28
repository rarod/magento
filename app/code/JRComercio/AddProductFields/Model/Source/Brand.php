<?php
 
namespace JRComercio\AddProductFields\Model\Source;
 
class Brand extends \Magento\Eav\Model\Entity\Attribute\Source\AbstractSource 
{
    public function getAllOptions() {
        if ($this->_options === null) {
            $this->_options = [
                ['label' => __('--Select--'), 'value' => ''],
                ['label' => __('VIPAL'), 'value' => 1],
                ['label' => __('WESTER'), 'value' => 2],
                ['label' => __('SELLE ROYAL'), 'value' => 3],
                ['label' => __('UNA'), 'value' => 4],
                ['label' => __('JKS'), 'value' => 5],
                ['label' => __('IMPORTADO'), 'value' => 6],
                ['label' => __('METALCICLO'), 'value' => 7],
                ['label' => __('PRÓ ROLL'), 'value' => 8],
                ['label' => __('UNICICLI'), 'value' => 9],
                ['label' => __('MS EXTENSORES'), 'value' => 10],
                ['label' => __('BONIATTI'), 'value' => 11],
                ['label' => __('CARRERA'), 'value' => 12],
                ['label' => __('NOBRE'), 'value' => 13],
                ['label' => __('VELOFORCE'), 'value' => 14],
                ['label' => __('DNZ'), 'value' => 15],
                ['label' => __('VZAN'), 'value' => 16],
                ['label' => __('HP'), 'value' => 17],
                ['label' => __('AMK'), 'value' => 18],
                ['label' => __('DS'), 'value' => 19],
                ['label' => __('NATHOR'), 'value' => 20],
                ['label' => __('NECO'), 'value' => 21],
                ['label' => __('ROMANO'), 'value' => 22],
                ['label' => __('SHIMANO'), 'value' => 23],
                ['label' => __('SHUNFENG'), 'value' => 24],
                ['label' => __('ELLEVEN'), 'value' => 25],
                ['label' => __('ORIGINAL BAG'), 'value' => 26],
                ['label' => __('PEAK'), 'value' => 27],
                ['label' => __('PTK'), 'value' => 28],
                ['label' => __('LONGHENG'), 'value' => 29],
                ['label' => __('KENLI'), 'value' => 30],
                ['label' => __('MIXIEER'), 'value' => 31],
                ['label' => __('FALCON'), 'value' => 32],
                ['label' => __('TEC'), 'value' => 33],
                ['label' => __('KMC'), 'value' => 34],
                ['label' => __('KALF'), 'value' => 35],
                ['label' => __('VELAMOS'), 'value' => 36],
                ['label' => __('FEIMIN'), 'value' => 37],
                ['label' => __('TAIWAN'), 'value' => 38],
                ['label' => __('YAMADA'), 'value' => 39],
                ['label' => __('METAL LINI'), 'value' => 40],
                ['label' => __('COMETA'), 'value' => 41],
                ['label' => __('HS BICYCLE'), 'value' => 42],
                ['label' => __('GANTECH'), 'value' => 43],
                ['label' => __('BICILEVES'), 'value' => 44],
                ['label' => __('SUNRACE'), 'value' => 45],
                ['label' => __('CLARKS'), 'value' => 46],
                ['label' => __('VEE RUBBER'), 'value' => 47],
                ['label' => __('TRUE'), 'value' => 48],
                ['label' => __('SAFETIRE'), 'value' => 49],
                ['label' => __('VP'), 'value' => 50],
                ['label' => __('MPEDA'), 'value' => 51],
                ['label' => __('ROCKBROS'), 'value' => 52],
                ['label' => __('ROSWHEEL'), 'value' => 53],
                ['label' => __('RSX'), 'value' => 54],
                ['label' => __('ALGOO'), 'value' => 55],
                ['label' => __('DDK'), 'value' => 56],
                ['label' => __('RIMAXX'), 'value' => 57],
                ['label' => __('SAHOO'), 'value' => 58],
                ['label' => __('YU QIU'), 'value' => 59],
                ['label' => __('MAXFORD'), 'value' => 60],
                ['label' => __('ABSOLUTE'), 'value' => 61],
                ['label' => __('SAIGUAN'), 'value' => 62],
                ['label' => __('BKZ'), 'value' => 63]
            ];
        }
        return $this->_options;
    }
}