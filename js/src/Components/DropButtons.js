import React, {useEffect} from 'react';

export const DropButtons = ({buttons, ...props}) => {

  const theseButtons = [...buttons];
  let firstButton = theseButtons.splice(0, 1)[0];
  let wrapperRef = React.createRef();

  useEffect(() => {
    try {
      Drupal.behaviors.dropButton.attach(wrapperRef.current, window.drupalSettings);
    }
    catch (e) {
      // Catch is mostly for webpack dev server.
    }
  }, []);

  return (
    <div ref={wrapperRef}>
      <div
        className={"dropbutton-wrapper dropbutton-multiple"}
        {...props}
      >
        <div className="dropbutton-widget">
          <ul className="dropbutton">
            <li>
              {firstButton}
            </li>

            {theseButtons.map((button, i) =>
              <li key={"button-" + i}>
                {button}
              </li>
            )}

          </ul>
        </div>
      </div>
    </div>
  )
};
