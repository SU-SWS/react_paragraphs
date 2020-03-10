import React from 'react';
import styled from 'styled-components'
import {DrupalContext} from "../WidgetManager";
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

const FieldContainer = styled.div`
  margin: 40px 0 0;
`;

export const ParagraphForm = ({formFields, ...props}) => {

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

  if (formFields === null || typeof formFields === 'undefined') {
    return (<div className="loading">Loading...</div>)
  }

  return (
    <div className="item-form">
      {Object.keys(formFields).map(fieldName => {
        const WidgetName = widgetComponents[formFields[fieldName].widget_type];

        if (WidgetName === undefined) {
          console.error('Unable to find widget for type: ' + formFields[fieldName].widget_type);
          return (
            <FieldContainer key={`widget-${props.item.id}-${fieldName}`}>
              Unable to provide a form for
              field {formFields[fieldName].label}
            </FieldContainer>
          )
        }
        return (
          <FieldContainer key={`widget-${props.item.id}-${fieldName}`}>
            <DrupalContext.Consumer>
              {drupalContext =>
                <WidgetName
                  fieldId={`${props.item.id}-${fieldName}`}
                  onFieldChange={drupalContext.updateParagraph.bind(undefined, props.item, fieldName)}
                  settings={formFields[fieldName]}
                  defaultValue={props.item.entity[fieldName]}
                  apiUrls={drupalContext.apiUrls}
                  fieldName={fieldName}
                  bundle={props.item.entity.type[0].target_id}
                />
              }
            </DrupalContext.Consumer>
          </FieldContainer>
        )
      })}
    </div>
  )

};
