import React, {Component} from "react";

export class ErrorBoundary extends Component {

  constructor(props) {
    super(props);
    this.state = { hasError: false };
  }

  componentDidCatch(error, info) {
    this.setState({ hasError: true });
    console.error(error);
  }

  render() {
    if (this.state.hasError) {
      return <div>{this.props.errorMessage}</div>;
    }
    return this.props.children;
  }
}
