import React from "react";
import {TextWidget} from "./Widgets/TextWidget";
import {SelectWidget} from "./Widgets/SelectWidget";
import {CheckboxesWidget} from "./Widgets/CheckboxesWidget";
import {RadiosWidget} from "./Widgets/RadiosWidget";

export const Behaviors = ({behaviors, widgetContext, entityType, entity}) => {

  const widgetComponents = {
    textfield: TextWidget,
    select: SelectWidget,
    checkbox: CheckboxesWidget,
    checkboxes: CheckboxesWidget,
    radios: RadiosWidget
  };

  const onFieldChange = (behaviorKey, fieldName, newValues) => {
    widgetContext.updateEntityBehaviors(entityType, entity, behaviorKey, fieldName, newValues);
  }

  const getFieldSettings = (field) => {
    const settings = {};
    Object.keys(field).map(key => {
      const newKey = key.replace('#', '');
      settings[newKey] = field[key];
    })
    settings.label = settings.title;
    settings.help = settings.description;
    settings.cardinality = (typeof settings.multiple !== 'undefined' && settings.multiple) ? -1 : 1;

    if (settings.type === 'checkbox') {
      settings.options = {1: settings.title};
    }
    return settings;
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
                />
              </div>
            )
          })}
        </div>
      )}
    </div>
  )
}
