import React from "react";
import {TextWidget} from "./Widgets/TextWidget";
import {LinkWidget} from "./Widgets/LinkWidget";
import {BooleanWidget} from "./Widgets/BooleanWidget";
import {DateWidget} from "./Widgets/DateWidget";
import {NumberWidget} from "./Widgets/NumberWidget";
import {SelectWidget} from "./Widgets/SelectWidget";
import {CheckboxesWidget} from "./Widgets/CheckboxesWidget";
import {RadiosWidget} from "./Widgets/RadiosWidget";
import {CkeditorWidget} from "./Widgets/CkeditorWidget";
import {MediaLibrary} from "./Widgets/MediaLibrary";
import {ViewFieldWidget} from "./Widgets/ViewFieldWidget";
import {Loader} from "./Atoms/Loader";
import styled from "styled-components";

export const EntityForm = ({entityType, bundle, entity, onFieldChange, drupalContext}) => {

  const widgetComponents = {
    text: TextWidget,
    link: LinkWidget,
    boolean: BooleanWidget,
    datetime: DateWidget,
    number: NumberWidget,
    select: SelectWidget,
    checkboxes: CheckboxesWidget,
    radios: RadiosWidget,
    ckeditor: CkeditorWidget,
    media_library: MediaLibrary,
    viewfield: ViewFieldWidget
  };

  const formFields = drupalContext.getEntityForm(entityType, bundle);
  if (typeof formFields === 'undefined') {
    return <Loader/>;
  }

  return (
    <div className="item-form">
      {Object.keys(formFields).map(fieldName => {
        const field = formFields[fieldName];
        const WidgetName = widgetComponents[field.widget_type];

        if (WidgetName === undefined) {
          console.error('Unable to find widget for type: ' + field.widget_type);
          return (
            <FieldContainer key={`widget-${item.id}-${fieldName}`}>
              Unable to provide a form for
              field {field.label}
            </FieldContainer>
          )
        }
        return (
          <FieldContainer key={`widget-${fieldName}`}>
            <WidgetName
              fieldId={fieldName}
              onFieldChange={onFieldChange.bind(undefined, fieldName)}
              settings={formFields[fieldName]}
              defaultValue={typeof entity[fieldName] !== 'undefined' ? entity[fieldName] : null}
              fieldName={fieldName}
              bundle={bundle}
            />
          </FieldContainer>
        )
      })}
    </div>
  )

}

const FieldContainer = styled.div`
  margin: 40px 0 0;
`;
