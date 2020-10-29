import React from "react";
import {ErrorBoundary} from './Atoms/ErrorBoundary';
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

  /**
   * When the field widget changes, adjust the values and pass them to context.
   *
   * @param behaviorKey
   * @param fieldName
   * @param newValues
   */
  const onFieldChange = (behaviorKey, fieldName, newValues) => {
    let behaviorValues = newValues;

    // The multiple values need some adjustment before saving.
    switch (behaviors[behaviorKey][fieldName]['#type']) {
      case 'checkbox':
        behaviorValues = behaviorValues.length >= 1 ? 1 : 0;
        break;

      case 'checkboxes':
        behaviorValues = newValues.map(item => item.value);
        break;

      case 'select':
        if (behaviors[behaviorKey][fieldName]['#multiple']) {
          behaviorValues = newValues.map(item => item.value);
          return onBehaviorChange(itemId, entityType, behaviorKey, fieldName, behaviorValues);
        }

      // Regular fields just have a single value and we just need that.
      default:
        const flattenedValues = flattenObject(newValues);
        const valueKey = Object.keys(flattenedValues)[0];
        behaviorValues = flattenedValues[valueKey];
    }

    onBehaviorChange(itemId, entityType, behaviorKey, fieldName, behaviorValues)
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

  /**
   * Get the field widget settings object.
   *
   * Since we are using the widgets similar to the regular content fields, we
   * need build an object with some settings.
   *
   * @param field
   * @returns {{}}
   */
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

  /**
   * Get the default value for the field.
   *
   * @param behaviorKey
   * @param fieldName
   * @returns {{value}[]|*[]|*}
   */
  const getDefaultBehaviorValue = (behaviorKey, fieldName) => {
    let default_value;

    if (
      typeof entity.behavior_settings === 'undefined' ||
      typeof entity.behavior_settings[0].value[behaviorKey] === 'undefined' ||
      typeof entity.behavior_settings[0].value[behaviorKey][fieldName] === 'undefined'
    ) {
      default_value = typeof behaviors[behaviorKey][fieldName]['#default_value'] === 'undefined' ? [] : behaviors[behaviorKey][fieldName]['#default_value'];
    }
    else {
      default_value = entity.behavior_settings[0].value[behaviorKey][fieldName];
    }

    switch (behaviors[behaviorKey][fieldName]['#type']) {
      case 'checkbox':
        // Convert true/false into integers for the field widget.
        default_value = default_value ? 1 : 0;
        break;

      case 'checkboxes':
        // Construct an array of values to match what the field widget wants.
        return default_value.map(item => {
          return ({value: item})
        });

      case 'select':
        // Construct an array of values to match what the field widget wants if
        // the select field allows multiple values.
        if (behaviors[behaviorKey][fieldName]['#multiple']) {
          return default_value.map(item => {
            return ({value: item})
          });
        }
        break;
    }

    return [{value: default_value}];
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
                <ErrorBoundary
                  errorMessage="An error occurred with a behavior field."
                >
                  <WidgetName
                    fieldId={fieldName}
                    settings={settings}
                    onFieldChange={onFieldChange.bind(undefined, behaviorKey, fieldName)}
                    fieldName={fieldName}
                    defaultValue={getDefaultBehaviorValue(behaviorKey, fieldName)}
                  />
                </ErrorBoundary>
              </div>
            )
          })}
        </div>
      )}
    </div>
  )
}
