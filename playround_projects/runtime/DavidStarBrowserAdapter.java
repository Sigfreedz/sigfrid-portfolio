import java.io.ByteArrayInputStream;
import java.io.ByteArrayOutputStream;
import java.io.FileOutputStream;
import java.io.PrintStream;
import java.nio.charset.StandardCharsets;

public class DavidStarBrowserAdapter {
    public static void main(String[] args) {
        String input = args.length > 0 ? args[0] : "";
        System.setIn(new ByteArrayInputStream((input + System.lineSeparator()).getBytes(StandardCharsets.UTF_8)));
        ByteArrayOutputStream output = new ByteArrayOutputStream();
        PrintStream capture = new PrintStream(output, true, StandardCharsets.UTF_8);
        PrintStream original = System.out;
        System.setOut(capture);
        try {
            DavidStar.main(new String[0]);
        } finally {
            System.setOut(original);
            capture.close();
            try (FileOutputStream file = new FileOutputStream("/files/davidstar-output.txt")) {
                file.write(output.toByteArray());
            } catch (Exception exception) {
                throw new IllegalStateException("Output capture unavailable.", exception);
            }
        }
    }
}
