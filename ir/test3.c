#include <stdio.h>
#include <stdlib.h>
#include <stdarg.h>
#include <unistd.h>
#include <string.h>
#include "irslinger.h"

int main(int argc, char *argv[])
{
	uint32_t outPin = 23;            // The Broadcom pin number the signal will be sent on
	int frequency = 38000;           // The frequency of the IR signal in Hz
	double dutyCycle = 0.5;          // The duty cycle of the IR signal. 0.5 means for every cycle,
	                                 // the LED will turn on for half the cycle time, and off the other half
	int leadingPulseDuration = 1000; // The duration of the beginning pulse in microseconds
	int leadingGapDuration = 1000;   // The duration of the gap in microseconds after the leading pulse
	int onePulse = 1000;              // The duration of a pulse in microseconds when sending a logical 1
	int zeroPulse = 10;             // The duration of a pulse in microseconds when sending a logical 0
	int oneGap = 1000;               // The duration of the gap in microseconds when sending a logical 1
	int zeroGap = 10;               // The duration of the gap in microseconds when sending a logical 0
	int sendTrailingPulse = 0;       // 1 = Send a trailing pulse with duration equal to "onePulse"
	                                 // 0 = Don't send a trailing pulse

	int result;


	while(1) {
		result = myIR2(outPin, frequency, dutyCycle);
	}
	
	return result;
}
