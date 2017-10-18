    <div id="myForm" style="height:auto;display:none;">
    <ul class="dhtmlxForm" name="myForm" oninit="doOnFormInit">
        <li ftype="settings" position="label-left" labelWidth="130" inputWidth="120"></li>
        <li ftype="fieldset" name="data" inputWidth="340">
            Welcome
            <ul>
                <li ftype="hidden" name="formid" value="12312389732492374"></li>
                <li ftype="radio" name="type1" labelWidth="auto" position="label-right" checked="true" value="1">
                    Already have account
                    <ul>
                        
                        <li ftype="input" value="p_rossi" name="login">Login</li>
                        <li ftype="password" value="123" name="password">Password</li>
                        <li ftype="checkbox" checked="true" name="remember">Remember me</li>
                    </ul>
                </li>
                <li ftype="radio" name="type1" labelWidth="auto" position="label-right" value="2">
                    Not registered yet
                    <ul>
                        <li ftype="input" value="Patricia D. Rossi" name="fullname">Full Name</li>
                        <li ftype="input" value="p_rossi@example.com" name="email">E-mail Address</li>
                        <li ftype="input" value="p_rossi" name="userlogin">Login</li>
                        <li ftype="password" value="123" name="userpass1">Password</li>
                        <li ftype="password" value="123" name="userpass2">Confirm Password</li>
                        <li ftype="checkbox" value="subscribe" name="subscribe">Subscribe on news</li>
                    </ul>
                </li>
                <li ftype="radio" name="type1" labelWidth="auto" position="label-right" value="3">
                    Guest login
                    <ul>
                        <li ftype="select" name="accounttype">
                            Account type
                            <ul>
                                <li ftype="option" value="admin">Admin</li>
                                <li ftype="option" value="org">Organiser</li>
                                <li ftype="option" value="poweruser">Power User</li>
                                <li ftype="option" value="user">User</li>
                            </ul>
                        </li>
                        <li ftype="checkbox" value="showlogs" name="showlogs">Show logs window</li>
                    </ul>
                </li>
                <li ftype="button" value="Proceed" name="save"></li>
            </ul>
        </li>
    </ul>
    </div>