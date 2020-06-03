import React from 'react';
import styled from 'styled-components'
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

const FieldContainer = styled.div`
  margin: 40px 0 0;
`;

export const ParagraphForm = ({item, drupalContext, ...props}) => {

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

  if (typeof item.loadedEntity !== 'undefined') {
    drupalContext.loadEntity(item.id);
    return <Loader/>;
  }

  const formFields = drupalContext.getFormFields(item);
  if (typeof formFields === 'undefined') {
    return <Loader/>;
  }

  return (
    <div className="item-form">
      {Object.keys(formFields).map(fieldName => {
        const WidgetName = widgetComponents[formFields[fieldName].widget_type];

        if (WidgetName === undefined) {
          console.error('Unable to find widget for type: ' + formFields[fieldName].widget_type);
          return (
            <FieldContainer key={`widget-${item.id}-${fieldName}`}>
              Unable to provide a form for
              field {formFields[fieldName].label}
            </FieldContainer>
          )
        }
        return (
          <FieldContainer key={`widget-${item.id}-${fieldName}`}>

            <WidgetName
              fieldId={`${item.id}-${fieldName}`}
              onFieldChange={drupalContext.updateParagraph.bind(undefined, item, fieldName)}
              settings={formFields[fieldName]}
              defaultValue={item.entity[fieldName]}
              fieldName={fieldName}
              bundle={item.entity.type[0].target_id}
            />
          </FieldContainer>
        )
      })}
    </div>
  )

};
