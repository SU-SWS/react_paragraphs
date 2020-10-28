import React from "react";
import {TextWidget} from "./Widgets/TextWidget";
import {SelectWidget} from "./Widgets/SelectWidget";
import {CheckboxesWidget} from "./Widgets/CheckboxesWidget";
import {RadiosWidget} from "./Widgets/RadiosWidget";

export const Behaviors = ({behaviors, onBehaviorChange, entityType, entity, itemId}) => {

  const widgetComponents = {
    textfield: TextWidget,
    select: SelectWidget,
    checkbox: CheckboxesWidget,
    checkboxes: CheckboxesWidget,
    radios: RadiosWidget
  };

  const onFieldChange = (behaviorKey, fieldName, newValues) => {
    const flattenedValues = flattenObject(newValues);
    const valueKey = Object.keys(flattenedValues)[0];
    onBehaviorChange(itemId, entityType, behaviorKey, fieldName, flattenedValues[valueKey])
  }

  /**
   * Flatten a multidimensional object
   *
   * For example:
   *   flattenObject({ a: 1, b: { c: 2 } })
   * Returns:
   *   { a: 1, c: 2}
   */
  const flattenObject = (obj) => {
    const flattened = {}

    Object.keys(obj).forEach((key) => {
      if (typeof obj[key] === 'object' && obj[key] !== null) {
        Object.assign(flattened, flattenObject(obj[key]))
      }
      else {
        flattened[key] = obj[key]
      }
    })

    return flattened
  }

  const getFieldSettings = (field) => {
    const settings = {};
    Object.keys(field).map(key => {
      const newKey = key.replace('#', '');
      settings[newKey] = field[key];
    })
    settings.label = settings.title;
    settings.help = settings.description;
    settings.column_key = 'value';
    settings.cardinality = (typeof settings.multiple !== 'undefined' && settings.multiple) ? -1 : 1;

    if (settings.type === 'checkbox') {
      settings.options = {1: settings.title};
    }
    return settings;
  }

  const getDefaultBehaviorValue = (behaviorKey, fieldName) => {
    if (
      typeof entity.behavior_settings === 'undefined' ||
      typeof entity.behavior_settings[0].value[behaviorKey] === 'undefined' ||
      typeof entity.behavior_settings[0].value[behaviorKey][fieldName] === 'undefined'
    ) {
      return [];
    }
    return [{value: entity.behavior_settings[0].value[behaviorKey][fieldName]}];
  }

  return (
    <div>
      {Object.keys(behaviors).map(behaviorKey =>
        <div key={behaviorKey}>
          {Object.keys(behaviors[behaviorKey]).map(fieldName => {
            const field = behaviors[behaviorKey][fieldName]
            const WidgetName = widgetComponents[field['#type']];
            const settings = getFieldSettings(field);

            if (typeof WidgetName === 'undefined') {
              return (
                <div key={`${behaviorKey}-${fieldName}`}>
                  Unable to provide behavior field.
                </div>
              )
            }

            return (
              <div key={`${behaviorKey}-${fieldName}`}>
                <WidgetName
                  fieldId={fieldName}
                  settings={settings}
                  onFieldChange={onFieldChange.bind(undefined, behaviorKey, fieldName)}
                  fieldName={fieldName}
                  defaultValue={getDefaultBehaviorValue(behaviorKey, fieldName)}
                />
              </div>
            )
          })}
        </div>
      )}
    </div>
  )
}
