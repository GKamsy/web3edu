<div class="notification-layout">

    <!-- =========== Sidebar or Left Panels ================= -->
    <aside class="notification-sidebar">
        <h2 class="empty"><u>ATTENTION!</u></h2></br>
        
        <p> 
            <strong>Please follow the steps below to create and post your notification:</strong>
        </p></br>
        
        <ol> <i>
            <li> 
                Select a recipient for your notification. 
                You may use the <strong>Search</strong> option to find the one you want.
            </li></br>
            <li> 
                Select the type of your notification from the <strong>Type</strong> box.
            </li></br>
            <li> 
                Write the title of your notification in the  <strong>Title</strong> box.
            </li></br>
            <li> 
                Write your message in the big <strong>Message</strong> box.
            </li></br>
            <li> 
                Click the <strong>Send Notification</strong> button to notify them.
            </li></br>
            <li> 
                You can click the red <strong>Cancel</strong> button to cancel and go back.
            </li> </i>
        </ol> </br> <strong>That's it!</strong>
    </aside>


    <!-- ================= Right Panel ================= -->
    <section id="notification-list" class="notification-list">
        <div class="compose-box">
            <h2>Create New Notification</h2><br/>

            <form method="POST" action="">

                <!-- Receiver -->
                <div class="form-group">
                    <label>Recipient (User ID):</label>
                    <input type="number" name="recipient_id" required>
                </div><br/>
                
                <!-- Notification Type -->
                <div class="form-group">
                    <label>Notification Type:</label>
                    <select name="type" required>
                        <option value="">-- Select Type --</option>
                        <option value="notification">Notification</option>
                        <option value="info">Information</option>
                        <option value="warning">Warning</option>
                        <option value="event">Event</option>
                        <option value="system">System</option>
                    </select>
                </div><br/>

                <!-- Title -->
                <div class="form-group">
                    <label>Title:</label>
                    <input type="text" name="title" maxlength="150" required>
                </div><br/>

                <!-- Optional Link -->
                <div class="form-group">
                    <label>Optional Link (URL):</label>
                    <input type="url" name="link" placeholder="https://example.com/page">
                </div><br/>

                <!-- Message -->
                <div class="form-group">
                    <label style="font-weight: bold;">Message:</label><br/>
                    <textarea name="message" rows="6" style="height: 300px; width: 70%;" required></textarea>
                </div><br/>

                <button type="submit" class="compose-btn">Send Notification</button> 

            </form>
            <a href="/notifications" 
               class="compose-btn" 
               style="width: 120px; background:red; font-weight:bold;">
               Cancel
            </a>
        </div>
    </section>
</div>
