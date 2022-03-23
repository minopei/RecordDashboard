import React, { useState } from "react";
import DatePicker from "react-datepicker";
import moment from "moment";

import "react-datepicker/dist/react-datepicker.css";

class Datepick extends Component {
  state = {
    startDate: new Date(),
  };

  render() {
    const { startDate } = this.state;
    return <DatePicker selected={startDate} onChange={this.handleChange} />;
  }

  handleChange = (startDate) => {
    this.setState({
      startDate,
    });
  };
}

if (document.getElementById('datepick')) {
  ReactDOM.render(<Datepick />, document.getElementById('datepick'));
}
