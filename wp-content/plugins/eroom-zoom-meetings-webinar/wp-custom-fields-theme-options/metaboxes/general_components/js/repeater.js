Vue.component('wpcfto_repeater', {
    props: ['fields', 'field_label', 'field_name', 'field_id', 'field_value'],
    data: function () {
        return {
            repeater: [],
            repeater_values: {}
        }
    },
    template: `
    <div class="wpcfto-repeater wpcfto_generic_field">
      
        <label v-html="field_label"></label>
        
        <p style="margin-bottom: 15px;" v-if="fields.description"><i v-html="fields.description"></i></p>
   
        <div class="addArea">
            <i class="fa fa-plus" @click="addArea"></i>
        </div>
        
        <div v-for="(area, area_key) in repeater" class="wpcfto-repeater-single" :class="'wpcfto-repeater_' + field_name + '_' + area_key ">
        
            <span class="wpcfto-repeater-single-key" :data-number="area_key + 1" @click="toggleArea(area)" :data-tab="field_label + ' (' + (area_key + 1) + ')'"></span>
            
            <div class="repeater_inner" :class="{'closed' : !area.closed_tab}">
           
                <div class="wpcfto-repeater-field" v-for="(field, field_name_inner) in fields.fields">
                
                    <component :is="'wpcfto_' + field.type"
                               :fields="field"
                               :field_name="field_name + '_' + area_key + '_' + field_name_inner"
                               :field_label="field.label"
                               :field_value="getFieldValue(area_key, field, field_name_inner)"
                               :field_data="field"
                               :field_native_name="field_name"
                               :field_native_name_inner="field_name_inner"
                               @wpcfto-get-value="$set(repeater[area_key], field_name_inner, $event)">
                    </component>
                   
                </div>
                
            </div>
                
            
            <i class="fa fa-times wpcfto-repeater-single-delete" @click="removeArea(area_key)"></i>
                
        </div>

    </div>
    `,
    mounted: function () {
        var _this = this;

        if (typeof _this.field_value === 'string' && WpcftoIsJsonString(_this.field_value)) {
            _this.field_value = JSON.parse(_this.field_value);
        }


        if (typeof _this.field_value !== 'undefined' && typeof _this.field_value !== 'string') {
            _this.$set(_this, 'repeater_values', _this.field_value);
            _this.repeater_values.forEach(function () {
                _this.repeater.push({});
            });
        }
    },
    methods: {
        addArea: function () {
            this.repeater.push({
                closed_tab : true
            });

            var el = 'wpcfto-repeater_' + this.field_name + '_' + (this.repeater.length - 1);

            Vue.nextTick(function () {
                if (typeof jQuery !== 'undefined') {
                    var $ = jQuery;
                    $([document.documentElement, document.body]).animate({
                        scrollTop: $("." + el).offset().top - 40
                    }, 400);
                }
            })

        },
        toggleArea: function(area) {
            var currentState  = (typeof area['closed_tab'] !== 'undefined') ? area['closed_tab'] : false;

            this.$set(area, 'closed_tab', !currentState);
        },
        removeArea: function (areaIndex) {
            if(confirm('Do your really want to delete this field?')) {
                this.repeater.splice(areaIndex, 1);
            }
        },
        getFieldValue(key, field, field_name) {

            if (typeof this.repeater_values === 'undefined') return field.value;
            if (typeof this.repeater_values[key] === 'undefined') return field.value;
            if (typeof this.repeater_values[key][field_name] === 'undefined') return field.value;

            return this.repeater_values[key][field_name];
        }
    },
    watch: {
        repeater: {
            deep: true,
            handler: function (repeater) {
                this.$emit('wpcfto-get-value', repeater);
            }
        },
    }
});