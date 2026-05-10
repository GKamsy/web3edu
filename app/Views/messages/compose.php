<!-- views/messages/messages.php -->

<div class="notification-layout">

    <!-- =========== Sidebar or Left Panels ================= -->
    <aside class="notification-sidebar">
        <h2 class="empty"><u>ATTENTION!</u></h2></br>
        
        <p> 
            <strong>Please follow the steps below to create and send your message:</strong>
        </p></br>
        
        <ol> <i>
            <li> 
                Write the email address of a recipient in the <strong>To</strong> box.
            </li></br>
            <li> 
                Write the subject (title) of your message in the  <strong>Subject</strong> box.
            </li></br>
            <li> 
                Write your message in the big <strong>Message</strong> box.
            </li></br>
            <li> 
                Click the <strong>Send Message</strong> button to notify them.
            </li></br>
            <li> 
                You can click the red <strong>Cancel</strong> button to cancel and go back.
            </li> </i>
        </ol> </br> <strong>That's it!</strong>
    </aside>


    <!-- ================= Right Panel ================= -->
    <section id="notification-list" class="notification-list">
        <div class="compose-box">
            <h2>Create New Message</h2><br/>

            <form method="POST" action="">
                <label>To:</label>
                <input type="number" name="receiver_id" required><br/><br/>

                <label>Subject:</label>
                <input type="text" name="subject" required><br/><br/>

                <label style="font-weight: bold;">Message:</label><br/>
                <textarea name="body" rows="6" style="height: 300px; width: 70%;" required></textarea><br/>

                <button type="submit" class="compose-btn">Send Message</button> 
            </form>
            <a href="/messages" class="compose-btn" style="width: 120px; background:red; font-weight:bold;">Cancel</a>
        </div>
    </section>
</div>
