@php
    Assets::addScriptsDirectly('vendor/core/core/base/js/repeater-field.js');
   
    
    $group = ''; $wrapper = false;
    foreach ($fields as $key => $field) {
        $item = Form::hidden($name . '[__key__][' . $key . '][key]', $field['attributes']['name']);
        $field['attributes']['name'] = $name . '[__key__][' . $key . '][value]';
        $field['attributes']['options']['id'] = md5($field['attributes']['name']);
        Arr::set($field, 'label_attr.for', $field['attributes']['options']['id']);
        $item .= Form::customLabel(Arr::get($field, 'attr.name'), $field['label'], Arr::get($field, 'label_attr')) .
        call_user_func_array([Form::class, $field['type']], array_values($field['attributes']));
        $item.="<span class='invalid-repeater-feedback'>This field is required.</span>";
        if(Arr::get($field,'wrapper')){
            $wrapper = true;
            $group .= '<div class="'.Arr::get($field,'wrapper.class').' ">' . $item . '</div>';
        }else{                
            $group .= '<div class="form-group">' . $item . '</div>';
        }
       
    }
    if($wrapper){
        $defaultFields = ['<div class="repeater-item-group form-group mb-3"><div class="row">' . $group . '</div></div>'];
    }else{    
        $defaultFields = ['<div class="repeater-item-group form-group mb-3">' . $group . '</div>'];
    }
    $values = (is_array($value)) ? $value : json_decode($value ?: '[]', true);

    $added = [];

    if (count($values) > 0) {
        for ($i = 0; $i < count($values); $i++) {
            $group = '';
            foreach ($fields as $key => $field) {
                $item = Form::hidden($name . '[' . $i . '][' . $key . '][key]', $field['attributes']['name']);
                $field['attributes']['name'] = $name . '[' . $i . '][' . $key . '][value]';
                $field['attributes']['value'] = Arr::get($values, $i . '.' . $key. '.value');
                $field['attributes']['options']['id'] = md5($field['attributes']['name']);
                Arr::set($field, 'label_attr.for', $field['attributes']['options']['id']);
                $item .= Form::customLabel(Arr::get($field, 'attr.name'), $field['label'], Arr::get($field, 'label_attr')) .
                call_user_func_array([Form::class, $field['type']], array_values($field['attributes']));
                $item.="<span class='invalid-repeater-feedback'>This field is required.</span>";
                if(Arr::get($field,'wrapper')){
                    $wrapper = true;
                    $group .= '<div class="'.Arr::get($field,'wrapper.class').' ">' . $item . '</div>';
                }else{                
                    $group .= '<div class="form-group">' . $item . '</div>';
                }
            }
            if($wrapper){
                $added[] = '<div class="repeater-item-group form-group mb-3"><div class="row">' . $group . '</div></div>';
            }else{    
                $added[] = '<div class="repeater-item-group form-group mb-3">' . $group . '</div>';
            }
            
        }
    }
@endphp

<repeater-component :fields="{{ json_encode($defaultFields) }}" :added="{{ json_encode($added) }}"></repeater-component>
