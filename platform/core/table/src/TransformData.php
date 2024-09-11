<?php


namespace Impiger\Table;

use Yajra\DataTables\Transformers\DataArrayTransformer;
/**
 * Description of TransformData
 *
 * @author sabarishankar.parthi
 */
class TransformData extends DataArrayTransformer{
     /**
     * Decode content to a readable text value.
     *
     * @param string $data
     * @return string
     */
    protected function decodeContent($data)
    {
        try {
            $decoded = html_entity_decode(strip_tags($data), ENT_QUOTES, 'UTF-8');            
            $decoded = trim(str_replace(PHP_EOL,'', $decoded));
            return str_replace("\xc2\xa0", ' ', $decoded);
        } catch (\Exception $e) {
            return $data;
        }
    }
}
