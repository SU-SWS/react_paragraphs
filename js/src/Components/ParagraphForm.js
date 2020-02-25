import React, {Component} from 'react';
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

export class ParagraphForm extends Component {

  widgetComponents = {
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

  constructor(props) {
    super(props);
    this.state = {
      formFields: {}
    };
    const url = this.props.apiUrls.baseDomain + this.props.apiUrls.formApi;

    fetch(url.replace('{entity_type_id}', 'paragraph').replace('{bundle}', this.props.item.entity.type[0].target_id))
      .then(response => response.json())
      .then(jsonData => {
        this.setState({formFields: jsonData})
      });
  }

  render() {
    if (Object.keys(this.state.formFields).length === 0) {
      return (<div className="loading">Loading...</div>)
    }

    return (
      <div className="item-form">
        <input data-drupal-selector="edit-node-test-media-form" type="hidden" name="form_id" value={this.props.item.entity.type[0].target_id + '_form'} />

        {Object.keys(this.state.formFields).map(fieldName => {
          const WidgetName = this.widgetComponents[this.state.formFields[fieldName].widget_type];

          if (WidgetName === undefined) {
            console.log('Unable to find widget for type: ' + this.state.formFields[fieldName].widget_type);
            return (
              <FieldContainer key={`widget-${this.props.item.id}-${fieldName}`}>
                Unable to provide a form for
                field {this.state.formFields[fieldName].label}
              </FieldContainer>
            )
          }
          return (
            <FieldContainer key={`widget-${this.props.item.id}-${fieldName}`}>
              <DrupalContext.Consumer>
                {drupalContext =>
                  <WidgetName
                    fieldId={`${this.props.item.id}-${fieldName}`}
                    onFieldChange={drupalContext.updateParagraph.bind(undefined, this.props.item, fieldName)}
                    settings={this.state.formFields[fieldName]}
                    defaultValue={this.props.item.entity[fieldName]}
                    apiUrls={drupalContext.apiUrls}
                    fieldName={fieldName}
                    bundle={this.props.item.entity.type[0].target_id}
                  />
                }
              </DrupalContext.Consumer>
            </FieldContainer>
          )
        })}
      </div>
    )
  }
}
